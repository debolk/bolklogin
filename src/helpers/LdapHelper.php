<?php
class LdapHelper
{
    static private $instance = null;

    static function Connect() {
        if(self::$instance == null)
            self::$instance = new LdapHelper;

        return self::$instance;
    }

    protected $ldap;
    protected $basedn;

    public function __construct()
    {
        $this->ldap = @ldap_connect(getenv('LDAP_HOST'));
        $this->basedn = getenv('LDAP_BASE');
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

    public function bind($uid, $pass)
    {
        $uid = $this->escapeArgument($uid);

        $users = ldap_search($this->ldap, $this->basedn, '(uid=' . $uid . ')');
        if(!$users || ldap_count_entries($this->ldap, $users) == 0)
            return false;
        $user = ldap_first_entry($this->ldap, $users);

        $dn = $user['dn'];

        return ldap_bind($this->ldap, $dn, $pass);
    }

	public function getName($uid) {
		$users = ldap_search($this->ldap, $this->basedn, "(uid=$uid)", ['givenName', 'fdNickname', 'sn']);
		if (!$users || ldap_count_entries($this->ldap, $users) == 0) {
			return false;
		}
		$user = ldap_first_entry($this->ldap, $users);
		$attrs = ldap_get_attributes($this->ldap, $user);
		if (isset($attrs['fdNickname'])) return "{$attrs['givenName']} \"{$attrs['fdNickname']}\" {$attrs['sn']}";
		else return "{$attrs['givenName']} {$attrs['sn']}";
	}

    public function memberOf($groupdn, $uid)
    {
        $groups = ldap_search($this->ldap, $groupdn, '(objectClass=posixGroup)');

        if(!$groups || ldap_count_entries($this->ldap, $groups) == 0)
            throw new Exception("Group '" . $groupdn . "' not found!");

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

    public function stripCounts($array)
    {
        unset($array['count']);
        foreach($array as $value)
            if(is_array($value))
                $this->stripCounts($value);
        return $array;
    }
}
