<?php
if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found');
}

/**
 * Use "public static function" instead of "static public function"
 * 
 * @author Raphael Malie
 */
class Prestashop_Sniffs_Functions_StaticAfterScopeSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{

    public function __construct()
    {
        parent::__construct(array(T_CLASS, T_INTERFACE), array(T_FUNCTION));

    }

    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

    	$previous = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
		if (in_array($tokens[$previous]['code'], PHP_CodeSniffer_Tokens::$scopeModifiers))
		{
			$scope = $tokens[$previous]['content'];

			// Now check for static keyword ...
			$previous = $phpcsFile->findPrevious(T_WHITESPACE, $previous - 1, null, true);
			if ($tokens[$previous]['code'] === T_STATIC)
			{
				$error = 'static keyword must be used after scope; use "%s static %s()"';
	            $phpcsFile->addError($error, $stackPtr, 'FunctionNeedScope', array(
	            	$scope,
	            	$phpcsFile->getDeclarationName($stackPtr),
	            ));
			}
		}
    }


}
