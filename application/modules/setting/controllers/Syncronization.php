<?php
// Function to check if the environment is production
function isProductionEnvironment() {
    // ENVIRONMENT == 'development';
    // Add logic here to check if the environment is production
    // For example, you can check if an environment variable is set or use other indicators specific to your setup
    // Return true if the environment is production, false otherwise
    return false;
    // return ($_SERVER['SERVER_NAME'] != 'localhost'); // Example: Check if the server name is not localhost

}

// Function to add a synchronization flag column to a table if it doesn't exist
function addSyncFlagToTable($connection, $tableName) {
    $alterQuery = "ALTER TABLE $tableName ADD COLUMN synchronized TINYINT(1) DEFAULT 0";
    $alterResult = mysqli_query($connection, $alterQuery);
    if (!$alterResult) {
        die("Error adding synchronization flag to table $tableName: " . mysqli_error($connection));
    }
}

// Function to synchronize changes from one database to another
function synchronizeDatabases($sourceConn, $destinationConn, $sourceDatabase, $destinationDatabase) {
    $updatedCount = 0; // Counter for updated records
    
    // Get list of tables from source database
    $tablesQuery = "SHOW TABLES";
    $tablesResult = mysqli_query($sourceConn, $tablesQuery);
    if (!$tablesResult) {
        die("Error fetching tables from source database: " . mysqli_error($sourceConn));
    }

    // Loop through each table
    while ($tableRow = mysqli_fetch_row($tablesResult)) {
        $tableName = $tableRow[0];

        // Check if the synchronization flag column already exists in the table
        $checkQuery = "SHOW COLUMNS FROM $tableName LIKE 'synchronized'";
        $checkResult = mysqli_query($sourceConn, $checkQuery);
        if (!$checkResult) {
            die("Error checking synchronization flag column in table $tableName: " . mysqli_error($sourceConn));
        }
        if (mysqli_num_rows($checkResult) == 0) {
            // Add synchronization flag column to the table if it doesn't exist
            addSyncFlagToTable($sourceConn, $tableName);
        }

        // Retrieve data from source table that is not synchronized
        $query = "SELECT * FROM $sourceDatabase.$tableName WHERE synchronized = 0";
        $result = mysqli_query($sourceConn, $query);
        if (!$result) {
            die("Error retrieving data from source table $tableName: " . mysqli_error($sourceConn));
        }

        // Loop through retrieved data and insert/update in destination database
        while ($row = mysqli_fetch_assoc($result)) {
            // Check if the data already exists in destination database
            $existingQuery = "SELECT * FROM $destinationDatabase.$tableName WHERE ";
            foreach ($row as $column => $value) {
                $existingQuery .= "$column = '" . mysqli_real_escape_string($destinationConn, $value) . "' AND ";
            }
            // Remove the trailing "AND" from the query
            $existingQuery = rtrim($existingQuery, "AND ");

            $existingResult = mysqli_query($destinationConn, $existingQuery);
            if (!$existingResult) {
                die("Error checking data in destination table $tableName: " . mysqli_error($destinationConn));
            }

            if (mysqli_num_rows($existingResult) > 0) {
                // Data exists, update
                $updateQuery = "UPDATE $destinationDatabase.$tableName SET ";
                foreach ($row as $column => $value) {
                    $updateQuery .= "$column = '" . mysqli_real_escape_string($destinationConn, $value) . "', ";
                }
                // Remove the trailing comma from the query
                $updateQuery = rtrim($updateQuery, ", ");
                $updateQuery .= " WHERE ";
                foreach ($row as $column => $value) {
                    $updateQuery .= "$column = '" . mysqli_real_escape_string($destinationConn, $value) . "' AND ";
                }
                // Remove the trailing "AND" from the query
                $updateQuery = rtrim($updateQuery, "AND ");

                $updateResult = mysqli_query($destinationConn, $updateQuery);
                if (!$updateResult) {
                    die("Error updating data in destination table $tableName: " . mysqli_error($destinationConn));
                }
                $updatedCount++; // Increment updated records count

                // Mark the record as synchronized in source database
                $markQuery = "UPDATE $sourceDatabase.$tableName SET synchronized = 1 WHERE ";
                foreach ($row as $column => $value) {
                    $markQuery .= "$column = '" . mysqli_real_escape_string($sourceConn, $value) . "' AND ";
                }
                // Remove the trailing "AND" from the query
                $markQuery = rtrim($markQuery, "AND ");

                $markResult = mysqli_query($sourceConn, $markQuery);
                if (!$markResult) {
                    die("Error marking record as synchronized in source table $tableName: " . mysqli_error($sourceConn));
                }
            } else {
                // Data doesn't exist, insert
                $insertQuery = "INSERT INTO $destinationDatabase.$tableName (";
                $insertValues = " VALUES (";
                foreach ($row as $column => $value) {
                    $insertQuery .= "$column, ";
                    $insertValues .= "'" . mysqli_real_escape_string($destinationConn, $value) . "', ";
                }
                // Remove the trailing commas from the query and values
                $insertQuery = rtrim($insertQuery, ", ");
                $insertValues = rtrim($insertValues, ", ");
                $insertQuery .= ")";
                $insertValues .= ")";
                $insertQuery .= $insertValues;

                $insertResult = mysqli_query($destinationConn, $insertQuery);
                if (!$insertResult) {
                    // Handle unique key conflict gracefully
                    if (strpos(mysqli_error($destinationConn), 'Duplicate entry') !== false) {
                        continue; // Skip insertion and proceed with the next record
                    } else {
                        die("Error inserting data into destination table $tableName: " . mysqli_error($destinationConn));
                    }
                }
                $updatedCount++; // Increment updated records count

                // Mark the record as synchronized in source database
                $markQuery = "UPDATE $sourceDatabase.$tableName SET synchronized = 1 WHERE ";
                foreach ($row as $column => $value) {
                    $markQuery .= "$column = '" . mysqli_real_escape_string($sourceConn, $value) . "' AND ";
                }
                // Remove the trailing "AND" from the query
                $markQuery = rtrim($markQuery, "AND ");

                $markResult = mysqli_query($sourceConn, $markQuery);
                if (!$markResult) {
                    die("Error marking record as synchronized in source table $tableName: " . mysqli_error($sourceConn));
                }
            }
        }
    }
    
    // Display progress message
    echo "Updated $updatedCount records.\n";
}

// Check if the environment is not production
if (!isProductionEnvironment()) {
    // Database connection parameters for live environment
    $liveHost = 'live_host';
    $liveUsername = 'live_username';
    $livePassword = 'live_password';
    $liveDatabase = 'live_database';

    // Connect to live database
    $liveConn = mysqli_connect($liveHost, $liveUsername, $livePassword, $liveDatabase);
    if (!$liveConn) {
        die("Connection to live database failed: " . mysqli_connect_error());
    }

    // Database connection parameters for local environment
    $localHost = 'local_host';
    $localUsername = 'local_username';
    $localPassword = 'local_password';
    $localDatabase = 'local_database';

    // Connect to local database
    $localConn = mysqli_connect($localHost, $localUsername, $localPassword, $localDatabase);
    if (!$localConn) {
        die("Connection to local database failed: " . mysqli_connect_error());
    }

    // Synchronize changes from local to live database
    synchronizeDatabases($localConn, $liveConn, $localDatabase, $liveDatabase);

    // Synchronize changes from live to local database
    synchronizeDatabases($liveConn, $localConn, $liveDatabase, $localDatabase);

    // Close connections
    mysqli_close($liveConn);
    mysqli_close($localConn);

    echo "Database synchronization complete.";
} else {
    echo "Database synchronization is not allowed in the production environment.";
}
?>
