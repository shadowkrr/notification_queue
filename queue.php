<?php
function addAlertQueue($queue)
{
    try {
        // Validate required fields
        if (!isset($queue['title']) || !isset($queue['text'])) {
            error_log('Alert queue missing required fields: title or text');
            return false;
        }

        // Ensure queues.json exists
        if (!file_exists("./queues.json")) {
            file_put_contents("./queues.json", json_encode([], JSON_UNESCAPED_UNICODE));
        }

        $queueData = file_get_contents("./queues.json");
        
        if ($queueData === false) {
            error_log('Failed to read queues.json');
            return false;
        }

        $queues = json_decode($queueData, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('Invalid JSON in queues.json: ' . json_last_error_msg());
            // Reset to empty array if JSON is corrupted
            $queues = [];
        }

        if (!is_array($queues)) {
            $queues = [];
        }

        // Check for duplicates (same title)
        $isDuplicate = false;
        foreach ($queues as $existingQueue) {
            if (isset($existingQueue['title']) && $existingQueue['title'] === $queue['title']) {
                $isDuplicate = true;
                break;
            }
        }

        // Add to queue if not duplicate
        if (!$isDuplicate) {
            // Ensure create timestamp is set
            if (!isset($queue['create'])) {
                $queue['create'] = date('c');
            }
            
            $queues[] = $queue;

            if (file_put_contents("./queues.json", json_encode($queues, JSON_UNESCAPED_UNICODE)) === false) {
                error_log('Failed to write to queues.json');
                return false;
            }
            
            return true;
        }
        
        return false; // Duplicate not added
        
    } catch (Exception $e) {
        error_log('Error in addAlertQueue: ' . $e->getMessage());
        return false;
    }
}
?>