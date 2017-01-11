<?php

namespace OroCRM\Bundle\SalesBundle\Tests\Functional\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\DashboardBundle\Entity\Dashboard;
use Oro\Bundle\DashboardBundle\Entity\Widget;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use OroCRM\Bundle\SalesBundle\Entity\Opportunity;

class LoadForecastWidgetFixtures extends AbstractFixture
{
    private $organization;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $this->organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();

        $this->addWidget($manager);
        $this->createOpportunity($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    private function addWidget(ObjectManager $manager)
    {
        $dashboard = new Dashboard();
        $dashboard->setName('Test dashboard');

        $leadStaticsWidget = new Widget();
        $leadStaticsWidget
            ->setDashboard($dashboard)
            ->setName('forecast_of_opportunities')
            ->setLayoutPosition([1, 1]);

        $dashboard->addWidget($leadStaticsWidget);

        if (!$this->hasReference('widget_forecast')) {
            $this->setReference('widget_forecast', $leadStaticsWidget);
        }
        $manager->persist($dashboard);
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function createOpportunity(ObjectManager $manager)
    {
        $opportunityList = [
            [
                'status' => 'in_progress',
                'close_date' => null,
                'probability' => 10, //percents
                'budget_amount' => 100, //USD
            ],
            [
                'status' => 'in_progress',
                'close_date' => new \DateTime('now'),
                'probability' => 10, //percents
                'budget_amount' => 100, //USD
            ],
            [
                'status' => 'in_progress',
                'close_date' => new \DateTime('now'),
                'probability' => 100, //percents
                'budget_amount' => 100, //USD
            ],
        ];

        foreach ($opportunityList as $opportunityName => $opportunityData) {
            $opportunity = new Opportunity();
            $opportunity->setName(sprintf('test_opportunity_%s', $opportunityName));
            $opportunity->setBudgetAmount($opportunityData['budget_amount']);
            $opportunity->setProbability($opportunityData['probability']);
            $opportunity->setOrganization($this->organization);
            $opportunity->setCloseDate($opportunityData['close_date']);

            $enumClass = ExtendHelper::buildEnumValueClassName(Opportunity::INTERNAL_STATUS_CODE);
            $opportunity->setStatus($manager->getReference($enumClass, $opportunityData['status']));

            $manager->persist($opportunity);
            $manager->flush();
        }
    }
}
