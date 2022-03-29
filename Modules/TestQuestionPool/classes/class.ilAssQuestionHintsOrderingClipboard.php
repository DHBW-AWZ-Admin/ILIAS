<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class for managing a clipboard via php session, that is used to re order a hint list
 * (user cuts a hint with a first request and is able to paste it at another position with a further request)
 *
 * @author		Björn Heyser <bheyser@databay.de>
 * @version		$Id$
 *
 * @package		Modules/TestQuestionPool
 */
class ilAssQuestionHintsOrderingClipboard
{
    /**
     * the id of question the stored hint relates to
     *
     * @access	private
     * @var		integer
     */
    private $questionId = null;
    
    /**
     * Constructor
     *
     * @access	public
     * @param	assQuestion	$questionOBJ
     */
    public function __construct(assQuestion $questionOBJ)
    {
        $this->questionId = $questionOBJ->getId();

        $class = ilSession::get(__CLASS__);
        if ($class == null) {
            ilSession::set(__CLASS__, array());
        }
        
        if (!isset($class[$this->questionId])) {
            $class[$this->questionId] = null;
            ilSession::set(__CLASS__, $class);
        }
    }
    
    /**
     * resets the clipboard by ensuring no hint is stored
     *
     * @access	public
     */
    public function resetStored()
    {
        $class = ilSession::get(__CLASS__);
        unset($class[$this->questionId]);
        ilSession::set(__CLASS__, $class);
        //$_SESSION[__CLASS__][$this->questionId] = null;
    }
    
    /**
     * sets the passed hint id, so relating hint
     * is deemed to be cut to clipboard
     *
     * @access	public
     * @param	integer	$hintId
     */
    public function setStored($hintId)
    {
        $class = ilSession::get(__CLASS__);
        $class[$this->questionId] = $hintId;
        ilSession::set(__CLASS__, $class);
        //$_SESSION[__CLASS__][$this->questionId] = $hintId;
    }
    
    /**
     * returns the hint id currently stored in clipboard
     *
     * @access	public
     * @return	integer $hintId
     */
    public function getStored() : int
    {
        $class = ilSession::get(__CLASS__);
        return $class[$this->questionId];
    }
    
    /**
     * returns the fact wether the hint relating to the passed hint id
     * is stored in clipboard or not
     *
     * @access	public
     * @param	integer	$hintId
     * @return	boolean	$isStored
     */
    public function isStored($hintId) : bool
    {
        $class = ilSession::get(__CLASS__);
        if ($class[$this->questionId] === $hintId) {
            return true;
        }
        
        return false;
    }
    
    /**
     * returns the fact wether any hint is stored in clipboard currently or not
     *
     * @access	public
     * @return	boolean $hasStored
     */
    public function hasStored() : bool
    {
        $class = ilSession::get(__CLASS__);
        if ($class[$this->questionId] !== null) {
            return true;
        }
        
        return false;
    }
}
