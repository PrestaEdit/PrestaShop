<?php
class Prestashop_Sniffs_CodeAnalysis_CastFollowedByParenthesisSniff implements PHP_CodeSniffer_Sniff
{
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$castTokens;

    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

		if ($tokens[$phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true)]['code'] === T_OPEN_PARENTHESIS)
		{
			$error = 'Cast "%s" followed by useless parenthesis';
			$phpcsFile->addWarning($error, $stackPtr, 'CastFollowedByParenthesis', array(
				$tokens[$stackPtr]['content'],
			));
		}
    }
}