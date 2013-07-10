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
        $this->ldap = ldap_connect(getenv('LDAP_HOST'));
        $this->basedn = getenv('LDAP_BASE');
    }

    public function escapeArgument($argument)
    {
        $sanitized=array('\\' => '\5c',
                     '*' => '\2a',
                     '(' => '\28',
                     ')' => '\29',
                     "\x00" => '\00');
        return str_replace(array_keys($sanitized),array_values($sanitized),$string);
    }

    public function bind($uid, $pass)
    {
        $uid = this->escapeArgument($uid);
        $users = ldap_search($this->ldap, $basedn, '(uid=' 
        return ldap_bind($this->ldap, $uid, $pass);
    }

    public function memberOf($groupdn, $uid)
    {
        $groups = @ldap_search($this->ldap, $groupdn, '(objectClass=posixGroup)');

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
