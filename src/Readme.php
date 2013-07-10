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
        return new Tonic\Response(200, file_get_contents('../README.md'), array('ContentType' => 'text/plain'));
    }
}
