<?php
if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found');
}

/**
 * Check if methods in classes have all a scope (public, protected, private)
 * 
 * @author Raphael Malie
 */
class Prestashop_Sniffs_Functions_FunctionNeedScopeSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{

    public function __construct()
    {
        parent::__construct(array(T_CLASS, T_INTERFACE), array(T_FUNCTION));

    }

    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

    	$previous = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
		if (!in_array($tokens[$previous]['code'], PHP_CodeSniffer_Tokens::$scopeModifiers))
		{
			// If static keyword found, check the keyword before
			if ($tokens[$previous]['code'] === T_STATIC)
			{
				$previous = $phpcsFile->findPrevious(T_WHITESPACE, $previous - 1, null, true);
				if (in_array($tokens[$previous]['code'], PHP_CodeSniffer_Tokens::$scopeModifiers))
					return ;
			}

            $error = 'function "%s()" need explicit "public" scope';
            $phpcsFile->addError($error, $stackPtr, 'FunctionNeedScope', array(
            	$phpcsFile->getDeclarationName($stackPtr),
            ));
		}
    }
}
