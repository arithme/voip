<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        <!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --c-gray-800: #333333;
            --c-gray-600: #666666;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            background-image: url('pic/background.png');
            background-size: cover;
            background-position: center;
        }

        .app {
            min-height: 80vh;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2vw 4vw 6vw;
            display: flex;
            flex-direction: column;
        }

        .app-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--c-gray-600);
        }

        .app-body-main-content {
            display: flex;
            justify-content: center;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .search-container {
            display: flex;
            align-items: center;
        }

        .search-form {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .search-input {
            margin-right: 10px;
            padding: 5px;
            font-size: 16px;
        }

        .table-select {
            padding: 5px;
            font-size: 16px;
        }

        .search-button {
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .search-results {
            margin-top: 20px;
        }

        .search-results table {
            width: 100%;
            border-collapse: collapse;
        }

        .search-results th,
        .search-results td {
            padding: 8px;
            border: 1px solid #ccc;
        }

        /* New CSS for the model count cards */
        .model-counts {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .model-card {
            width: 100px;
            height: 60px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 0.75rem;
            font-weight: 600;
            color: #fff;
            border-radius: 6px;
        }

        .model-card.model-3905 {
            background-color: #007bff;
        }

        .model-card.model-6941 {
            background-color: #28a745;
        }

        .model-card.model-7821 {
            background-color: #6c757d;
        }

        
    </style>
</head>
<body>
    <div class="app">
        <div class="app-header">
            <ul>
                <li><a href="index.php">Dial-100</a></li>
                <li><a href="allipphones.php">District IPT</a></li>
                <li><a href="directory.php">Important Phone Directory</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
            </ul>
            <form class="search-form" method="POST" action="">
                <input type="text" name="query" class="search-input" placeholder="Enter search query" required>
                <select name="table" class="table-select">
                    <option value="" disabled selected>Select a table</option>
                    <option value="additional_data">Summary</option>
                    <option value="all_ip_phones">District</option>
                    <option value="directory">PHQ/HO</option>
                    <option value="ip_phones">Dial-100 </option>
                </select>
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>
        <div class="app-body">
            <!-- Body content -->
            <div class="search-results">
                <?php echo isset($searchResults) ? $searchResults : ''; ?>
            </div>
            <div class="model-counts">
                <div class="model-card model-3905">
                    <div class="model-count-title">Model 3905</div>
                    <div class="model-count-value"><?php echo isset($model3905Count) ? $model3905Count : 0; ?></div>
                </div>
                <div class="model-card model-6941">
                    <div class="model-count-title">Model 6941</div>
                    <div class="model-count-value"><?php echo isset($model6941Count) ? $model6941Count : 0; ?></div>
                </div>
                <div class="model-card model-7821">
                    <div class="model-count-title">Model 7821</div>
                    <div class="model-count-value"><?php echo isset($model7821Count) ? $model7821Count : 0; ?></div>
                </div>
                <!-- Add other model counts here if needed -->
            </div>
        </div>
    </div>
</body>
</html>

    </style>
</head>
<body>
    <div class="app">
        <div class="app-header">
             <li><a href="index.php">Dial-100</a></li>
            <li><a href="allipphones.php">District IPT</a></li>
            <li><a href="directory.php">Important Phone Directory</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <form class="search-form" method="POST" action="">
                <input type="text" name="query" class="search-input" placeholder="Enter search query" required>
                <select name="table" class="table-select">
                    <option value="" disabled selected>Select a table</option>
                    <option value="additional_data">Summary</option>
                    <option value="all_ip_phones">District</option>
                    <option value="directory">PHQ/HO</option>
                    <option value="ip_phones">Dial-100 </option>
                </select>
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>
        <div class="app-body">
            <!-- Body content -->
            <div class="search-results">
                <?php echo $searchResults; ?>
            </div>
            <div class="model-counts">
                <div class="model-card model-3905">
                    <div class="model-count-title">Model 3905</div>
                    <div class="model-count-value"><?php echo $model3905Count; ?></div>
                </div>
                <div class="model-card model-6941">
                    <div class="model-count-title">Model 6941</div>
                    <div class="model-count-value"><?php echo $model6941Count; ?></div>
                </div>
                <div class="model-card model-7821">
                    <div class="model-count-title">Model 7821</div>
                    <div class="model-count-value"><?php echo $model7821Count; ?></div>
                </div>
                <!-- Add other model counts here if needed -->
            </div>
        </div>
    </div>
</body>
</html>
