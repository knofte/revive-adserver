<?php

require_once MAX_PATH . '/extensions/deliveryLog/logCommon.php';

class Plugins_DeliveryLog_OxLogImpression_LogImpressionCountry extends Plugins_DeliveryLog_LogCommon
{
    function getDependencies()
    {
        return array(
            'deliveryLog:oxLogImpression:logImpressionCountry' => array(
                'deliveryDataPrepare:oxDeliveryDataPrepare:dataCommon',
                'deliveryDataPrepare:oxDeliveryDataPrepare:dataGeo',
            )
        );
    }

    function getBucketName()
    {
        return 'data_bucket_impression_country';
    }

    public function getTableBucketColumns()
    {
        $columns = array(
            'interval_start' => self::TIMESTAMP_WITHOUT_ZONE ,
            'creative_id' => self::INTEGER,
            'zone_id' => self::INTEGER,
            'country' => self::CHAR,
            'count' => self::INTEGER,
        );
    }
}

?>