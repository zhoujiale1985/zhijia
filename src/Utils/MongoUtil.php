<?php

namespace ZhijiaCommon\Utils;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\Exception;

class MongoUtil
{
    public static function sortByKey($handle, $request)
    {
        if ($request->input('sort_key')) {
            return $handle->orderBy($request->input('sort_key'), $request->input('sort_direction', 'asc'));
        }
        return $handle;
    }

    public static function multipleUpdate($filterList, $dataList, $collectionName)
    {
        // mongo config
        $host = env('MONGODB_HOST');
        $port = env('MONGODB_PORT');
        $username = env('MONGODB_USERNAME');
        $password = env('MONGODB_PASSWORD');
        $database = env('MONGODB_DATABASE');
        $uri = "mongodb://$username:$password@$host:$port";

        $manager = new Manager($uri);
        $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

        $timestamp = time();
        foreach ($filterList as $index => $filter) {
            $data = $dataList[$index];
            $data['update_time'] = $timestamp;
            $bulk->update($filter, ['$set' => $data]);
        }

        try {
            $result = $manager->executeBulkWrite("$database.$collectionName", $bulk, $writeConcern);
        } catch (BulkWriteException $e) {
            $result = $e->getWriteResult();

            // Check if the write concern could not be fulfilled
            if ($writeConcernError = $result->getWriteConcernError()) {
                $log = sprintf("%s (%d): %s\n",
                    $writeConcernError->getMessage(),
                    $writeConcernError->getCode(),
                    var_export($writeConcernError->getInfo(), true));
                Log::error($log);
                return $this->fail($log);
                return [
                    'code' => -1,
                    'msg' => $log,
                ];
            }

            // Check if any write operations did not complete at all
            foreach ($result->getWriteErrors() as $writeError) {
                $log = sprintf("Operation#%d: %s (%d)\n",
                    $writeError->getIndex(),
                    $writeError->getMessage(),
                    $writeError->getCode());
                Log::error($log);
                return [
                    'code' => -1,
                    'msg' => $log,
                ];
            }
        } catch (Exception $e) {
            $log = printf("Other error: %s\n", $e->getMessage());
            Log::error($log);
            return [
                'code' => -1,
                'msg' => $log,
            ];
        }
        return [
            'code' => 0,
            'msg' => $result,
        ];
    }
}
