<?php
class LdapHelper
{
    static private $instance = null;

    static function Connect() : LdapHelper {
        if(self::$instance == null)
            syslog(LOG_ERR, "Connection to LDAP server not initialised!");

        return self::$instance;
    }

	static function Initialise(string $ldap_host, string $ldap_base): LdapHelper {
		self::$instance = new LdapHelper($ldap_host, $ldap_base);
		return self::$instance;
	}

    protected $ldap;
    protected $basedn;
	protected $starttls;

    public function __construct($ldap_host, $ldap_base)
    {
        $this->ldap = @ldap_connect($ldap_host);
        $this->basedn = $ldap_base;
        ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3); //sets ldap protocol to v3; the server won't accept otherwise
	    $this->starttls = ldap_start_tls($this->ldap);
    }

	public function getStartTLS(): bool {
		return $this->starttls;
	}

    public function escapeArgument($argument)
    {
        $sanitized=array('\\' => '\5c',
                     '*' => '\2a',
                     '(' => '\28',
                     ')' => '\29',
                     "\x00" => '\00');
        return str_replace(array_keys($sanitized),array_values($sanitized),$argument);
    }

	public function getBaseDn() {
		return $this->basedn;
	}

	private function getDn(string $uid): string|bool {
		$uid = $this->escapeArgument($uid);

		$users = ldap_search($this->ldap, $this->basedn, '(uid=' . $uid . ')');
		if(!$users || ldap_count_entries($this->ldap, $users) == 0)
			return false;
		return ldap_get_dn($this->ldap, ldap_first_entry($this->ldap, $users));
	}

    public function bind($uid, $pass): bool {
		$user = $this->getDn($uid);
		if (is_bool($user)) {
			return false;
		}
        return ldap_bind($this->ldap, $user, $pass);
    }

	public function getName($uid) {
		$users = ldap_search($this->ldap, $this->basedn, "(uid=$uid)", ['givenName', 'fdNickname', 'sn']);
		if (!$users || ldap_count_entries($this->ldap, $users) == 0) {
			return false;
		}
		$user = ldap_get_attributes($this->ldap, ldap_first_entry($this->ldap, $users));
		if (isset($user['fdNickname'])) return "{$user['givenName'][0]} \"{$user['fdNickname'][0]}\" {$user['sn'][0]}";
		else return "{$user['givenName'][0]} {$user['sn'][0]}";
	}

	/**
	 * @throws Exception
	 */
	public function memberOf($groupdn, $uid): bool {

		$groups = ldap_search($this->ldap, $groupdn, '(|(objectClass=posixGroup)(objectClass=organizationalUnit))');

		if (!$groups || ldap_count_entries($this->ldap, $groups) == 0) {
			throw new Exception("Group '" . $groupdn . "' not found!");
		}

        $groups = $this->stripCounts(ldap_get_entries($this->ldap, $groups));
        
        $group = null;
        foreach($groups as $g)
            if($g['dn'] == $groupdn)
            {
                $group = $g;
                break;
            }

        if(!isset($group['memberuid']))
            return false;

        if(!in_array($uid, $group['memberuid']))
            return false;

        return true;
    }

	public function getPasswordReset(string $uid): bool {
		$users = ldap_search($this->ldap, $this->basedn, "(uid=$uid)", ['pwdReset']);
		if (!$users || ldap_count_entries($this->ldap, $users) == 0) {
			return false;
		}
		$pwdReset = ldap_get_attributes($this->ldap, ldap_first_entry($this->ldap, $users));
		$pwdReset = $this->stripCounts($pwdReset['pwdReset']);
		return isset($pwdReset) && (is_bool($pwdReset) && $pwdReset)
			|| (is_array($pwdReset) && isset($pwdReset[0]) && $pwdReset[0] == true);
	}

	public function set_password(string $uid, #[\SensitiveParameter] string $old_password = "", #[\SensitiveParameter] string $new_password = "") : string|bool {
		$user = $this->getDn($uid);
		if (is_bool($user)) {
			return false;
		}
		if (!ldap_bind($this->ldap, $user, $old_password)) {
			return false;
		}
		return @ldap_exop_passwd($this->ldap, $user, $old_password, $new_password);
	}

    public function stripCounts($array)
    {
        unset($array['count']);
        foreach($array as $value)
            if(is_array($value))
                $this->stripCounts($value);
        return $array;
    }

	/**
	 * Returns the last thrown error
	 * @return string						the last error returned from ldap
	 */
	public function lastError() : string
	{
		return ldap_error($this->ldap);
	}

	public function lastErrorNo() : int{
		return ldap_errno($this->ldap);
	}
}
