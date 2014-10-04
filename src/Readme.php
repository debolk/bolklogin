<?php
/**
 * @uri /
 */
class Readme extends Tonic\Resource
{
    /**
     * @method GET
     */
    public function getIndex()
    {
        return new Tonic\Response(200, file_get_contents('../web/instructions.html'), array('ContentType' => 'text/html'));
    }
}
