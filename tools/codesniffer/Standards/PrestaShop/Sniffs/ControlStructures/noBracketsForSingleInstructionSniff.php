<?php
/**
 * Warn about brackets for single intruction line
 * 
 * 	if ($foo)
 * 	{
 * 		something();
 * 	}
 * will trigger a warning.
 * 
 * 	if ($foo)
 * 	{
 * 		while ($bar)
 * 			something();
 * 	}
 * won't trigger any warning.
 * 
 * @author Raphael Malie
 */
class Prestashop_Sniffs_ControlStructures_noBracketsForSingleInstructionSniff implements PHP_CodeSniffer_Sniff
{
	protected static $instructionsKeywords = array(
                T_IF,
                T_WHILE,
                T_FOREACH,
                T_FOR,
                T_SWITCH,
                T_DO,
                T_ELSE,
                T_ELSEIF,
               );

    public function register()
    {
        return self::$instructionsKeywords;

    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
		$tokens = $phpcsFile->getTokens();
		
		// We don't need to check instructions without brackets
		if (!isset($tokens[$stackPtr]['scope_opener']))
			return ;

		$diffLines = $tokens[$tokens[$stackPtr]['scope_closer']]['line'] - $tokens[$tokens[$stackPtr]['scope_opener']]['line'] - 1;
		if ($diffLines == 1)
		{
            $error = 'Do not use brackets { } for single line in "%s"';
            $phpcsFile->addError($error, $stackPtr, 'BracketsForSingleInstruction', array(
            	$tokens[$stackPtr]['content'].' ()',
            ));
		}
    }
}
