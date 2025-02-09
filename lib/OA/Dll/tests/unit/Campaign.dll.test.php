<?php

/*
+---------------------------------------------------------------------------+
| Revive Adserver                                                           |
| http://www.revive-adserver.com                                            |
|                                                                           |
| Copyright: See the COPYRIGHT.txt file.                                    |
| License: GPLv2 or later, see the LICENSE.txt file.                        |
+---------------------------------------------------------------------------+
*/

require_once MAX_PATH . '/lib/OA/Dll/Advertiser.php';
require_once MAX_PATH . '/lib/OA/Dll/AdvertiserInfo.php';
require_once MAX_PATH . '/lib/OA/Dll/Campaign.php';
require_once MAX_PATH . '/lib/OA/Dll/CampaignInfo.php';
require_once MAX_PATH . '/lib/OA/Dll/tests/util/DllUnitTestCase.php';

/**
 * A class for testing DLL Campaign methods
 *
 * @package    OpenXDll
 * @subpackage TestSuite
 */


class OA_Dll_CampaignTest extends DllUnitTestCase
{
    /**
     * @var int
     */
    public $agencyId;

    /**
     * Errors
     *
     */
    public $unknownIdError = 'Unknown campaignId Error';

    /**
     * The constructor method.
     */
    public function __construct()
    {
        parent::__construct();
        Mock::generatePartial(
            'OA_Dll_Campaign',
            'PartialMockOA_Dll_Campaign_CampaignTest',
            ['checkPermissions']
        );
        Mock::generatePartial(
            'OA_Dll_Advertiser',
            'PartialMockOA_Dll_Advertiser_CampaignTest',
            ['checkPermissions', 'getDefaultAgencyId']
        );
    }

    public function setUp()
    {
        $this->agencyId = DataGenerator::generateOne('agency');
    }

    public function tearDown()
    {
        DataGenerator::cleanUp();
    }

    /**
     * A method to test Add, Modify and Delete.
     */
    public function testAddModifyDelete()
    {
        $dllAdvertiserPartialMock = new PartialMockOA_Dll_Advertiser_CampaignTest($this);
        $dllCampaignPartialMock = new PartialMockOA_Dll_Campaign_CampaignTest($this);

        $dllAdvertiserPartialMock->setReturnValue('getDefaultAgencyId', $this->agencyId);
        $dllAdvertiserPartialMock->setReturnValue('checkPermissions', true);
        $dllAdvertiserPartialMock->expectCallCount('checkPermissions', 2);

        $dllCampaignPartialMock->setReturnValue('checkPermissions', true);
        $dllCampaignPartialMock->expectCallCount('checkPermissions', 5);

        $oAdvertiserInfo = new OA_Dll_AdvertiserInfo();
        $oAdvertiserInfo->advertiserName = 'test Advertiser name';
        $oAdvertiserInfo->agencyId = $this->agencyId;

        $dllAdvertiserPartialMock->modify($oAdvertiserInfo);

        $oCampaignInfo = new OA_Dll_CampaignInfo();

        $oCampaignInfo->advertiserId = $oAdvertiserInfo->advertiserId;

        // Add
        $this->assertTrue(
            $dllCampaignPartialMock->modify($oCampaignInfo),
            $dllCampaignPartialMock->getLastError()
        );

        // Modify
        $this->assertTrue(
            $dllCampaignPartialMock->modify($oCampaignInfo),
            $dllCampaignPartialMock->getLastError()
        );

        // Delete
        $this->assertTrue(
            $dllCampaignPartialMock->delete($oCampaignInfo->campaignId),
            $dllCampaignPartialMock->getLastError()
        );

        // Modify not existing id
        $this->assertTrue(
            (!$dllCampaignPartialMock->modify($oCampaignInfo) &&
                          $dllCampaignPartialMock->getLastError() == $this->unknownIdError),
            $this->_getMethodShouldReturnError($this->unknownIdError)
        );

        // Delete not existing id
        $this->assertTrue(
            (!$dllCampaignPartialMock->delete($oCampaignInfo->campaignId) &&
                           $dllCampaignPartialMock->getLastError() == $this->unknownIdError),
            $this->_getMethodShouldReturnError($this->unknownIdError)
        );

        $dllCampaignPartialMock->tally();
    }

    /**
     * A method to test get and getList method.
     */
    public function testGetAndGetList()
    {
        $dllAdvertiserPartialMock = new PartialMockOA_Dll_Advertiser_CampaignTest($this);
        $dllCampaignPartialMock = new PartialMockOA_Dll_Campaign_CampaignTest($this);

        $dllAdvertiserPartialMock->setReturnValue('getDefaultAgencyId', $this->agencyId);
        $dllAdvertiserPartialMock->setReturnValue('checkPermissions', true);
        $dllAdvertiserPartialMock->expectCallCount('checkPermissions', 2);

        $dllCampaignPartialMock->setReturnValue('checkPermissions', true);
        $dllCampaignPartialMock->expectCallCount('checkPermissions', 6);

        $oAdvertiserInfo = new OA_Dll_AdvertiserInfo();
        $oAdvertiserInfo->advertiserName = 'test Advertiser name';
        $oAdvertiserInfo->agencyId = $this->agencyId;

        $dllAdvertiserPartialMock->modify($oAdvertiserInfo);

        $oCampaignInfo1 = new OA_Dll_CampaignInfo();
        $oCampaignInfo1->campaignName = 'test name 1';
        $oCampaignInfo1->impressions = 10;
        $oCampaignInfo1->clicks = 2;
        $oCampaignInfo1->priority = 5;
        $oCampaignInfo1->weight = 0;
        $oCampaignInfo1->advertiserId = $oAdvertiserInfo->advertiserId;

        $oCampaignInfo2 = new OA_Dll_CampaignInfo();
        $oCampaignInfo2->campaignName = 'test name 2';
        $oCampaignInfo2->startDate = new Date('2001-01-01 00:00:00');
        $oCampaignInfo2->endDate = new Date('2021-01-01 23:59:59');
        $oCampaignInfo2->advertiserId = $oAdvertiserInfo->advertiserId;
        // Add
        $this->assertTrue(
            $dllCampaignPartialMock->modify($oCampaignInfo1),
            $dllCampaignPartialMock->getLastError()
        );

        $this->assertTrue(
            $dllCampaignPartialMock->modify($oCampaignInfo2),
            $dllCampaignPartialMock->getLastError()
        );

        $oCampaignInfo1Get = null;
        $oCampaignInfo2Get = null;
        // Get
        $this->assertTrue(
            $dllCampaignPartialMock->getCampaign(
                $oCampaignInfo1->campaignId,
                $oCampaignInfo1Get
            ),
            $dllCampaignPartialMock->getLastError()
        );
        $this->assertTrue(
            $dllCampaignPartialMock->getCampaign(
                $oCampaignInfo2->campaignId,
                $oCampaignInfo2Get
            ),
            $dllCampaignPartialMock->getLastError()
        );

        // Check field value
        $this->assertFieldEqual($oCampaignInfo1, $oCampaignInfo1Get, 'campaignName');
        $this->assertFieldEqual($oCampaignInfo1, $oCampaignInfo1Get, 'startDate');
        $this->assertFieldEqual($oCampaignInfo1, $oCampaignInfo1Get, 'endDate');
        $this->assertFieldEqual($oCampaignInfo1, $oCampaignInfo1Get, 'impressions');
        $this->assertFieldEqual($oCampaignInfo1, $oCampaignInfo1Get, 'clicks');
        $this->assertFieldEqual($oCampaignInfo1, $oCampaignInfo1Get, 'priority');
        $this->assertFieldEqual($oCampaignInfo1, $oCampaignInfo1Get, 'weight');
        $this->assertFieldEqual($oCampaignInfo1, $oCampaignInfo1Get, 'advertiserId');
        $this->assertFieldEqual($oCampaignInfo2, $oCampaignInfo2Get, 'campaignName');
        $this->assertFieldEqual($oCampaignInfo2, $oCampaignInfo2Get, 'startDate');
        $this->assertFieldEqual($oCampaignInfo2, $oCampaignInfo2Get, 'endDate');

        // Get List
        $aCampaignList = [];
        $this->assertTrue(
            $dllCampaignPartialMock->getCampaignListByAdvertiserId(
                $oAdvertiserInfo->advertiserId,
                $aCampaignList
            ),
            $dllCampaignPartialMock->getLastError()
        );
        $this->assertEqual(
            count($aCampaignList) == 2,
            '2 records should be returned'
        );
        $oCampaignInfo1Get = $aCampaignList[0];
        $oCampaignInfo2Get = $aCampaignList[1];
        if ($oCampaignInfo1->campaignId == $oCampaignInfo2Get->campaignId) {
            $oCampaignInfo1Get = $aCampaignList[1];
            $oCampaignInfo2Get = $aCampaignList[0];
        }
        // Check field value from list
        $this->assertFieldEqual($oCampaignInfo1, $oCampaignInfo1Get, 'campaignName');
        $this->assertFieldEqual($oCampaignInfo2, $oCampaignInfo2Get, 'campaignName');


        // Delete
        $this->assertTrue(
            $dllCampaignPartialMock->delete($oCampaignInfo1->campaignId),
            $dllCampaignPartialMock->getLastError()
        );

        // Get not existing id
        $this->assertTrue(
            (!$dllCampaignPartialMock->getCampaign(
                $oCampaignInfo1->campaignId,
                $oCampaignInfo1Get
            ) &&
                          $dllCampaignPartialMock->getLastError() == $this->unknownIdError),
            $this->_getMethodShouldReturnError($this->unknownIdError)
        );

        $dllCampaignPartialMock->tally();
    }

    /**
     * Method to run all tests for campaign statistics
     *
     * @access private
     *
     * @param string $methodName  Method name in Dll
     */
    public function _testStatistics($methodName)
    {
        $dllAdvertiserPartialMock = new PartialMockOA_Dll_Advertiser_CampaignTest($this);
        $dllCampaignPartialMock = new PartialMockOA_Dll_Campaign_CampaignTest($this);

        $dllAdvertiserPartialMock->setReturnValue('getDefaultAgencyId', $this->agencyId);
        $dllAdvertiserPartialMock->setReturnValue('checkPermissions', true);
        $dllAdvertiserPartialMock->expectCallCount('checkPermissions', 2);

        $dllCampaignPartialMock->setReturnValue('checkPermissions', true);
        $dllCampaignPartialMock->expectCallCount('checkPermissions', 5);

        $oAdvertiserInfo = new OA_Dll_AdvertiserInfo();
        $oAdvertiserInfo->advertiserName = 'test Advertiser name';
        $oAdvertiserInfo->agencyId = $this->agencyId;

        $dllAdvertiserPartialMock->modify($oAdvertiserInfo);

        $oCampaignInfo = new OA_Dll_CampaignInfo();

        $oCampaignInfo->advertiserId = $oAdvertiserInfo->advertiserId;

        // Add
        $this->assertTrue(
            $dllCampaignPartialMock->modify($oCampaignInfo),
            $dllCampaignPartialMock->getLastError()
        );

        // Get no data
        $rsCampaignStatistics = null;
        $this->assertTrue($dllCampaignPartialMock->$methodName(
            $oCampaignInfo->campaignId,
            new Date('2001-12-01'),
            new Date('2007-09-19'),
            false,
            $rsCampaignStatistics
        ), $dllCampaignPartialMock->getLastError());

        $this->assertTrue(isset($rsCampaignStatistics));

        // Handle array result sets
        if (is_array($rsCampaignStatistics)) {
            $this->assertEqual(count($rsCampaignStatistics), 0, 'No records should be returned');

        // Handle MDB2 result sets
        } elseif ($rsCampaignStatistics instanceof MDB2_Result_Common) {
            $this->assertEqual($rsCampaignStatistics->numRows(), 0, 'No records should be returned');

        // Handle DBC (deprecated) result sets
        } else {
            $this->assertEqual($rsCampaignStatistics->getRowCount(), 0, 'No records should be returned');
        }

        // Test for wrong date order
        $rsCampaignStatistics = null;
        $this->assertTrue(
            (!$dllCampaignPartialMock->$methodName(
                $oCampaignInfo->campaignId,
                new Date('2007-09-19'),
                new Date('2001-12-01'),
                false,
                $rsCampaignStatistics
            ) &&
            $dllCampaignPartialMock->getLastError() == $this->wrongDateError),
            $this->_getMethodShouldReturnError($this->wrongDateError)
        );

        // Delete
        $this->assertTrue(
            $dllCampaignPartialMock->delete($oCampaignInfo->campaignId),
            $dllCampaignPartialMock->getLastError()
        );

        // Test statistics for not existing id
        $rsCampaignStatistics = null;
        $this->assertTrue(
            (!$dllCampaignPartialMock->$methodName(
                $oCampaignInfo->campaignId,
                new Date('2001-12-01'),
                new Date('2007-09-19'),
                false,
                $rsCampaignStatistics
            ) &&
            $dllCampaignPartialMock->getLastError() == $this->unknownIdError),
            $this->_getMethodShouldReturnError($this->unknownIdError)
        );

        $dllCampaignPartialMock->tally();
    }

    /**
     * A method to test getCampaignZoneStatistics.
     */
    public function testDailyStatistics()
    {
        $this->_testStatistics('getCampaignDailyStatistics');
    }

    /**
     * A method to test getCampaignZoneStatistics.
     */
    public function testHourlyStatistics()
    {
        $this->_testStatistics('getCampaignHourlyStatistics');
    }

    /**
     * A method to test getCampaignZoneStatistics.
     */
    public function testBannerStatistics()
    {
        $this->_testStatistics('getCampaignBannerStatistics');
    }

    /**
     * A method to test getCampaignZoneStatistics.
     */
    public function testPublisherStatistics()
    {
        $this->_testStatistics('getCampaignPublisherStatistics');
    }

    /**
     * A method to test getCampaignZoneStatistics.
     */
    public function testZoneStatistics()
    {
        $this->_testStatistics('getCampaignZoneStatistics');
    }

    public function testConversionStatistics()
    {
        $this->_testStatistics('getCampaignConversionStatistics');
    }
}
