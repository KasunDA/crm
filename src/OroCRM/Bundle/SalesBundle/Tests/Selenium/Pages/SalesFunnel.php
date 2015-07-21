<?php

namespace OroCRM\Bundle\SalesBundle\Tests\Selenium\Pages;

use Oro\Bundle\TestFrameworkBundle\Pages\AbstractPageEntity;

/**
 * Class SalesFunnel
 *
 * @package OroCRM\Bundle\SalesBundle\Tests\Selenium\Pages
 */
class SalesFunnel extends AbstractPageEntity
{
    /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element */
    protected $startDate;
      /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element */
    protected $lead;
    /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element */
    protected $opportunity;

    protected $owner = "//div[starts-with(@id,'s2id_oro_workflow_transition_sales_funnel_owner')]/a";

    public function __construct($testCase, $redirect = true)
    {
        parent::__construct($testCase, $redirect);
    }

    public function setStartDate($date)
    {
        $this->startDate = $this->test->byXpath("//*[@data-ftid='oro_workflow_transition_sales_funnel_start_date']/..".
            "/following-sibling::input[contains(@class,'datepicker-input')]");
        $this->startDate->clear();
        $this->startDate->value($date);

        return $this;
    }

    public function setLead($lead)
    {
        $this->lead = $this->test->byXpath("//div[starts-with(@id,'s2id_oro_workflow_transition_lead')]/a");
        $this->lead->click();
        $this->waitForAjax();
        $this->test->byXpath("//div[@id='select2-drop']/div/input")->value($lead);
        $this->waitForAjax();
        $this->assertElementPresent(
            "//div[@id='select2-drop']//div[contains(., '{$lead}')]",
            "Lead autocomplete doesn't return search value"
        );
        $this->test->byXpath("//div[@id='select2-drop']//div[contains(., '{$lead}')]")->click();

        return $this;
    }

    public function selectEntity($type, $entity)
    {
        $this->opportunity = $this->test->byXpath(
            "(//button[contains(@class, 'entity-select-btn')])[last()]"
        );
        $this->opportunity->click();
        $this->waitPageToLoad();
        $this->waitForAjax();
        $this->test->byXpath("//div[@class='filter-container']//a[contains(., '{$type} name')]")->click();
        $filter = $this->test->byXpath(
            "//div[contains(@class, 'filter-item oro-drop open-filter' )]//input[@name='value']"
        );

        $filter->clear();
        $filter->value($entity);
        $this->test->byXPath(
            "//div[contains(@class, 'filter-item oro-drop open-filter' )]//button[contains(., 'Update')]"
        )->click();
        $this->waitForAjax();
        $this->test->byXpath(
            "//table[@class='grid table-hover table table-bordered table-condensed']//td[contains(., '{$entity}')]"
        )->click();
        sleep(1);
        $this->waitPageToLoad();
        $this->waitForAjax();

        return $this;
    }

    public function submit()
    {
        $this->test->byXpath("//button[@id='save-and-transit']")->click();
        $this->waitPageToLoad();
        $this->waitForAjax();

        return new Workflow($this->test);
    }

    public function edit()
    {
        $this->test->byXpath(
            "//div[@class='pull-left btn-group icons-holder']/a[@title = 'Edit sales process']"
        )->click();
        $this->waitPageToLoad();
        $this->waitForAjax();

        return $this;
    }

    public function delete()
    {
        $this->test->byXpath("//div[@class='pull-left btn-group icons-holder']/a[contains(., 'Delete')]")->click();
        $this->test->byXpath("//div[div[contains(., 'Delete Confirmation')]]//a[text()='Yes, Delete']")->click();
        $this->waitPageToLoad();
        $this->waitForAjax();

        return new SalesFunnels($this->test, false);
    }
}
