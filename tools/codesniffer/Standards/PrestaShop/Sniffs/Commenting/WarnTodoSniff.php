<?php

/**
 * Display a warning for todo
 * 
 * @author Raphael
 */
class Prestashop_Sniffs_Commenting_WarnTodoSniff implements PHP_CodeSniffer_Sniff
{
    public function register()
    {
        return array(T_COMMENT, T_DOC_COMMENT);
    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (preg_match('#(// todo|@todo) (.*)$#i', $tokens[$stackPtr]['content'], $m))
        {
            $error = 'TODO found: %s';
            $phpcsFile->addWarning($error, $stackPtr, 'TodoFound', array(
            	trim($m[2]),
            ));
        }
    }
}
