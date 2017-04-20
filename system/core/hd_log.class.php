<?php
class hd_log {

    const EMERG     = 'EMERG';
    const ALERT     = 'ALERT';
    const CRIT      = 'CRIT';
    const ERR       = 'ERR';
    const WARN      = 'WARN';
    const NOTICE    = 'NOTIC';
    const INFO      = 'INFO';
    const DEBUG     = 'DEBUG';
    const SQL       = 'SQL';
    
	static function record($info,$level = '',$record = '') {
        return true;
	}

    static function write($message) {
        return true;
    }

}