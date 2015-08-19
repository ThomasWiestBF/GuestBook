<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 04.08.2015
 * Time: 11:19
 */

namespace AppBundle\Model;


use Symfony\Component\Filesystem\Filesystem;

class GuestBookFileModel {
    protected $FileSystem;
    protected $Entries;
    const FILE_NAME = 'storage\guestbook.db';

    public function __construct(){
        $this->FileSystem = new Filesystem();

        //perform initial file existance check
        if(!$this->FileSystem->exists(self::FILE_NAME)){
            $this->FileSystem->touch(self::FILE_NAME);
            throw new \Exception("FUCK! All our guestBook entries disappeared! Created new File!");
        }
        $this->readFile();
    }

    /**
     * Read the Database file and save the Entries as a run-time temp array
     */
    protected function readFile(){
        $this->Entries = unserialize(file_get_contents(self::FILE_NAME));
    }

    /**
     * Add a new Entry to the Entry list
     * @param $objEntry
     */
    public function addNewEntry($objEntry){
        $this->Entries[] =  $objEntry;
    }

    /**
     * Delete an Entry from the Entry list
     * @param $handle
     */
    public function deleteEntry($handle){
        unset($this->Entries[$handle]);
    }

    /**
     * Write our entries to the Database-file
     */
    protected function writeFile(){
        file_put_contents(self::FILE_NAME, serialize($this->Entries));
    }

    /**
     * Returns all Saved Entries with its ID specified as attribute (set on runtime)
     * @return mixed
     */
    public function getAllEntries(){
        foreach($this->Entries as $intID => $entry){
            $this->Entries[$intID]->ID = $intID;
        }
        return $this->Entries;
    }

    public function __destruct(){
        $this->writeFile();
    }
}