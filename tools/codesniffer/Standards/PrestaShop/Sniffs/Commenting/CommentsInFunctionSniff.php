<?php
if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found');
}

/**
 * Display a warning for todo
 *
 * @author Raphael
 */
class Prestashop_Sniffs_Commenting_CommentsInFunctionSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{
    public function __construct()
    {
        parent::__construct(array(T_FUNCTION), array(T_COMMENT, T_DOC_COMMENT), true);

    }

    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        // Issues with this sniff, let's desactivate temporary comments checks in fucntions
        return;

        // Comments with // are allowed inside functions
        if ($tokens[$stackPtr]['code'] == T_COMMENT)
        	return ;

		$error = 'Bad comment type in function: %s';
		$phpcsFile->addError($error, $stackPtr, 'BadCommentType', array(
			trim($tokens[$stackPtr]['content']),
		));
    }

    protected function processTokenOutsideScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
    	$tokens = $phpcsFile->getTokens();

    	// Comments with /* */ are allowed outside functions
    	if (!preg_match('#^//#', $tokens[$stackPtr]['content']))
        	return ;

        $error = 'Bad comment type outside of function: %s';
		$phpcsFile->addError($error, $stackPtr, 'BadCommentTypeOutside', array(
			trim($tokens[$stackPtr]['content']),
		));
    }
}
