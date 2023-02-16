<?php

class zaver_admindetails extends oxAdminDetails
{
    /**
     * Init needed data
     */
    public function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * Returns factory instance of given classname
     * 
     * @param  string $sClassName
     * @return object
     */
    public function zvpoGetInstance($sClassName)
    {
        return oxNew($sClassName);
    }
    
}
