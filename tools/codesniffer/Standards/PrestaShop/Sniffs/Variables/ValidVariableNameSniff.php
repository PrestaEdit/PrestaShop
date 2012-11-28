<?php
/**
 * Check variables names
 *
 * 	valid : $is_ok, $my_test, $i_love_prestashop
 * 	not valid :	$isOK, $mY_test, $IlOvEpReStAsHoP
 *
 * @author Raphael Malie
 */
class Prestashop_Sniffs_Variables_ValidVariableNameSniff implements PHP_CodeSniffer_Sniff
{
	public $exceptions = array(
		'fieldsRequired',
		'fieldsSize',
		'fieldsValidate',
		'fieldsRequiredLang',
		'fieldsSizeLang',
		'fieldsValidateLang',
		'webserviceParameters',
		'langMultiShop',
		'currentIndex',
		'tabAccess',
	);

    public function register()
    {
        return array(T_VARIABLE);
    }

    protected function makeRealName($varname)
    {
		$real_name = $varname;
		$real_name = preg_replace_callback('#([A-Z])([A-Z]*)#', create_function('$m', 'return \'_\'.strtolower($m[1].$m[2]);'), $real_name);
		$real_name = preg_replace('#_{2,}#', '_', $real_name);
		$real_name = preg_replace('#^_+#', '', $real_name);
		return $real_name;
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $varname = ltrim($tokens[$stackPtr]['content'], '$');

        // Ignore PHP vars
        $keywords = array('_SERVER', '_GET', '_POST', '_REQUEST', '_SESSION', '_ENV', '_COOKIE', '_FILES', 'GLOBALS');
        if (in_array($varname, $keywords))
        	return;

        // Check if variable name is valid
        if (!in_array($varname, $this->exceptions) && !preg_match('#^[a-z][a-z0-9]*(_[a-z0-9]+)*$#', $varname))
        {
            $error = 'Variable "%s" have not right syntaxe. Should be : "%s"';
            $phpcsFile->addWarning($error, $stackPtr, 'VariableNameNotValid', array(
            	'$'.$varname,
            	'$'.$this->makeRealName($varname),
            ));
        }

        // Now check if there is an object member after the variable
        $next = $phpcsFile->findNext(array(T_WHITESPACE), ($stackPtr + 1), null, true);
        if ($tokens[$next]['code'] === T_OBJECT_OPERATOR)
        {
			$nextMember = $phpcsFile->findNext(array(T_WHITESPACE), ($next + 1), null, true);
			if ($tokens[$nextMember]['code'] === T_STRING)
			{
				// Check if this is not a function
				$nextBracket = $objOperator = $phpcsFile->findNext(array(T_WHITESPACE), ($nextMember + 1), null, true);
                if ($tokens[$nextBracket]['code'] !== T_OPEN_PARENTHESIS)
                {
                	$membername = $tokens[$nextMember]['content'];
                	if (!in_array($membername, $this->exceptions) && !preg_match('#^[_a-z][a-z0-9]*(_[a-z0-9]+)*$#', $membername))
                	{
			            $error = 'Variable "%s" have not right syntaxe. Should be: "%s"';
			            $phpcsFile->addWarning($error, $stackPtr, 'VariableNameNotValid', array(
			            	'->'.$membername,
			            	'->'.$this->makeRealName($membername),
			            ));
                	}
                }
			}
        }
    }
}
