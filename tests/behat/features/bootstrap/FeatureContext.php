<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\MinkExtension\Context\MinkContext,
    Behat\Mink\Exception\ExpectationException,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

$sDirRoot = dirname(realpath((dirname(__FILE__)) . "/../../../../../"));
set_include_path(get_include_path().PATH_SEPARATOR.$sDirRoot);

require_once("tests/behat/features/bootstrap/BaseFeatureContext.php");

/**
 * LiveStreet custom feature context
 */
class FeatureContext extends MinkContext
{
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->useContext('base', new BaseFeatureContext($parameters));
    }

    public function getEngine() {
        return $this->getSubcontext('base')->getEngine();
    }

    /**
     * Тест ищет все совпадения значений в таблице, в предлах одной строки TR (Проходит только если все из перечисленных значений найдены)
     *
     * @Then /^element "([^"]*)" should contain values:$/
     */
    public function elementShouldContain($elementXpath, TableNode $value)
    {
        $element = $this->getSession()->getPage()->find('css', "$elementXpath");
        if ($element) {
            $content = str_replace(array("\r\n", "\n"), '',$element->getHtml());
            $pattern = '/<tr.*>(.*)<\/tr>/Ui';

            if (!preg_match_all($pattern, $content, $parsedArray)) {
                throw new ExpectationException('Element parse fail', $this->getSession());
            }

            foreach ($parsedArray[0] as $parsedItem) {
                $flag = true;
                foreach ($value->getHash() as $valueItem) {
                    $regex  = '/'.preg_quote($valueItem['value'], '/').'/ui';
                    if (!preg_match($regex, $parsedItem)) {
                        $flag = false;
                    }
                }

                if ($flag) {
                    return true;
                }
            }

            throw new ExpectationException("Items not found in element", $this->getSession());
        }
        else {
            throw new ExpectationException('Container not found', $this->getSession());
        }
    }

    /**
     * @Then /^I send js message "([^"]*)" to element by css "([^"]*)"$/
     */
    public function iSendJsMessageToElementByCss( $evenMessage, $xPAth )
    {
        $element = $this->getSession()->getPage()->find('css', "$xPAth");
        if ($element) {
            $this->getSession()->executeScript("$('{$xPAth}').{$evenMessage}");
        }
        else {
            throw new ExpectationException('Container not found', $this->getSession());
        }
    }

    /**
     * @Then /^element by css "([^"]*)" should have structure:$/
     */
    public function elementByCssShouldHaveStructure($cssElement, PyStringNode $stringList)
    {
        $pattern = array("\n", "\r\n", "\t", " ");
        $collapsedTestStrings = str_replace($pattern, '', $stringList->getRaw());

        $element = $this->getSession()->getPage()->find('css', "$cssElement");
        if ($element) {
            $collapsedResponseString = str_replace($pattern, '', $element->getHtml());

            if ($collapsedResponseString != $collapsedTestStrings) {
                throw new ExpectationException('Structure incorrect', $this->getSession());
            }
        }
        else {
            throw new ExpectationException('Container not found', $this->getSession());
        }
    }

    /**
     * @Then /^I should see element "([^"]*)" values in order:$/
     */
    public function iShouldSeeElementValuesInOrder($elementXpath, TableNode $table)
    {
        $element = $this->getSession()->getPage()->find('css', $elementXpath);
        if ($element) {
            $elementHtml = str_replace(array("\r\n", "\n"), '',$element->getHtml());
            $regex = '/';
            foreach ($table->getHash() as $valueItem) {
                $regex .= '(' . preg_quote($valueItem['value'], '/'). ').*';
            }
            $regex = trim($regex, '.*') .'/';

            if (!preg_match($regex, $elementHtml)) {
                throw new ExpectationException('Elements order fail', $this->getSession());
            }
        }
        else {
            throw new ExpectationException('Container not found', $this->getSession());
        }
   }


}





