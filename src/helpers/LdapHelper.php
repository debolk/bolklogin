<?php
class LdapHelper
{
    static private $instance = null;

    static function Connect() : LdapHelper {
        if(self::$instance == null)
            syslog(LOG_ERR, "Connection to LDAP server not initialised!");

        return self::$instance;
    }

	static function Initialise($ldap_host, $ldap_base): void {
		self::$instance = new LdapHelper($ldap_host, $ldap_base);
	}

    protected $ldap;
    protected $basedn;

    public function __construct($ldap_host, $ldap_base)
    {
        $this->ldap = @ldap_connect($ldap_host);
        $this->basedn = $ldap_base;
        ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3); //sets ldap protocol to v3; the server won't accept otherwise
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

    public function bind($uid, $pass)
    {
        $uid = $this->escapeArgument($uid);

        $users = ldap_search($this->ldap, $this->basedn, '(uid=' . $uid . ')');
        if(!$users || ldap_count_entries($this->ldap, $users) == 0)
            return false;
		$user = ldap_get_dn($this->ldap, ldap_first_entry($this->ldap, $users));

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
		if (str_starts_with($groupdn, "ou=people")) return $this->inOrganizationUnit($groupdn, $uid);

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

	private function inOrganizationUnit($oudn, $uid): bool {
		$users = ldap_search($this->ldap, $oudn, "(&(objectClass=fdBolkData)(uid=$uid))");

		if (!$users || ldap_count_entries($this->ldap, $users) == 0) return false;

		return true;
	}

    public function stripCounts($array)
    {
        unset($array['count']);
        foreach($array as $value)
            if(is_array($value))
                $this->stripCounts($value);
        return $array;
    }
}
