<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="author" content="colorlib.com">
	<title>Art Katz Transcript Searcher</title>
	<style>
    body {
      font-size: medium;
    }
    select {
      margin-top: 20px;
    }
  </style>
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet" />
    <link href="css/main.css" rel="stylesheet" />
  </head>
  <body>
    <div class="s003">
    <form action="index.php" method="post">
	
	<table width="100%" height="100%" border="0" cellspacing="20" cellpadding="0">
    <tr>
        <td align="center" valign="middle">
            <center> <!-- Deprecated but still works for horizontal centering -->
                <a href="index.php">
                    <img src="art.png" alt="Art Katz" class="responsive" style="border:0;"> <!-- border:0 ensures no border around clickable image -->
                </a>
				<h2>Search For a Word or Phrase in Art Katz's Sermons</h2>
            </center>
        </td>
    </tr>
	</table>
        <div class="inner-form">
            <div class="input-field first-wrap">
                <div class="input-select">
                    <select id="fontSize" name="textSize" data-trigger="">
						<option placeholder="">Text Size</option>
                        <option value="x-large">X-Large</option>
						<option value="large">Large</option>
						<option value="medium">Medium</option>
						<option value="small">Small</option>
						<option value="x-small">X-Small</option>
						</select>
                </div>
            </div>
            <div class="input-field second-wrap">
                <input id="search" type="text" placeholder="What are you looking for?" name="searchQuery" />
            </div>
            <div class="input-field third-wrap">
                <button class="btn-search" type="submit">
                    <svg class="svg-inline--fa fa-search fa-w-16" aria-hidden="true" data-prefix="fas" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
                    </svg>
                </button>
            </div>
        </div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
      const dropdown = document.getElementById('fontSize');

      dropdown.addEventListener('change', function() {
        document.body.style.fontSize = this.value;
      });
    });
  </script>



		<?PHP
		if (!isset($_POST['searchQuery'])) 
		{}
		else{

			
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (isset($_POST['searchQuery'])) {
					$search_word = $_POST['searchQuery'];

				} else {
					echo "Word not received.";
				}
			} else {
				echo "Invalid request method.";
			}
			//$search_word = 'And so my faith';
			$directory_path = 'timestamps'; // Replace with the path to your directory

			// Function to search for the word in a file and return matching lines
			function searchWordInFile($file_path, $search_word) {
				$file_lines = file($file_path, FILE_IGNORE_NEW_LINES);
				$found_lines = array();
				$maxresults2 = 1;
				
				foreach ($file_lines as $line) {
					if (stripos($line, $search_word) !== false) {
						$found_lines[] = $line;
						if ($maxresults2 == 15) return $found_lines;
						$maxresults2++;
					}
				}

				return $found_lines;
			}

			// Function to recursively search for the word in all files in a directory
			function searchWordInDirectory($directory_path, $search_word) {
				$found_lines = array();

				if (is_dir($directory_path)) {
					$files = scandir($directory_path);
					$maxresults = 1;
					foreach ($files as $file) {
						if ($file !== '.' && $file !== '..') {
							$file_path = $directory_path . DIRECTORY_SEPARATOR . $file;

							if (is_file($file_path)) {
								$found_in_file = searchWordInFile($file_path, $search_word);

								if (!empty($found_in_file)) {
									$found_lines[$file] = $found_in_file;
									if ($maxresults == 100) {
										echo "<br><b>" , $search_word , "</b> was found in more than " , $maxresults , " sermons. Only displaying " , $maxresults , " sermons.<br>" ; 
										return $found_lines;
										}
									$maxresults++;
								}
							} elseif (is_dir($file_path)) {
								$found_in_subdirectory = searchWordInDirectory($file_path, $search_word);
								if (!empty($found_in_subdirectory)) {
									$found_lines = array_merge($found_lines, $found_in_subdirectory);
									
								}
							}
						}
					}
				}
				return $found_lines;
			}

			// Search for the word in the directory and its subdirectories
			$found_in_files = searchWordInDirectory($directory_path, $search_word);

			// Output the results
			if (!empty($found_in_files)) {
				echo "<br>Found the word '<b>{$search_word}</b>' in the following sermons:\n<br><br>";
				foreach ($found_in_files as $file => $found_lines) {
					$file = substr($file, 0, -4);
					$fileName = "transcripts/" . $file . " - Art Katz.txt";
					echo "In sermon: <b>";
					?><a href="index.php?file=<?php echo urlencode($fileName); ?>"><?php echo $file; ?></a> 
					<a href="<?php echo $fileName; ?>" download style="float: right;"><?php echo "Download File"; ?></a></b>
					<br clear="both"><?php
					foreach ($found_lines as $found_line) {
						$found_line_with_bold = str_replace($search_word, "<b>$search_word</b>", $found_line);
						echo $found_line_with_bold . "\n<br><br>";
					}
					echo "\n<br>";
				}
			} else {
				echo "<br><b>'{$search_word}'</b> was not found in any sermon.";
			}
		}
		?>
		<?php
		if (isset($_GET['file'])) {
			$file_path = $_GET['file'];
			if (file_exists($file_path)) {
				$file_content = file_get_contents($file_path);
				$lines = explode("\n", $file_content);
				if (count($lines) > 0) {
					$lines[0] = '<strong>' . htmlspecialchars($lines[0]) . '</strong>';
				}
				// Modify the file path for displaying and linking
				$lines[0] .= ' <a href="download.php?file_path=' . urlencode($file_path) . '">   Download file</a>';
				echo "<br>" , nl2br(implode("\n", $lines));
				
			} else {
				echo "File not found.";
			}
		} 
		
		if (isset($_GET['notes'])) { // for when a user clicks on "Read Me"
			$file_path = 'notes.txt';
			if (file_exists($file_path)) {
				$file_contents = file_get_contents($file_path);
				$file_contents_with_breaks = nl2br($file_contents);
				echo "<br>" ,$file_contents_with_breaks;
			} else {
				echo "The file does not exist.";
			}
		}
		
		
		if (isset($_GET['list'])) { // for when a user clicks on "Complete List Of Transcripts"
			$directory = 'transcripts';
			// Get a list of all .txt files in the directory
			$files = glob($directory . '/*.txt');
			// Loop through each file and create a downloadable link
			echo "<ul>";
			foreach ($files as $file) {
				$filename = basename($file);
				$filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
				echo "<li><a href='$directory/$filename' download>$filenameWithoutExt</a></li>";
			}
			echo "</ul>";
		}
		?>
		
		
    </form>
	
	<?php 
	if (!isset($_GET['file']) && !isset($_POST['searchQuery']) && !isset($_GET['notes']) && !isset($_GET['list'])): 
	?>
		<div class="bottom-text">
			<a href="index.php?notes">Read Me</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="index.php?list">Complete List Of Transcripts</a>
		</div>
	<?php 
	endif; 
	?>
	

    <script src="js/extention/choices.js"></script>
    <script>
      const choices = new Choices('[data-trigger]',
      {
        searchEnabled: false,
        itemSelectText: '',
      });

    </script>
	</font>
  </body><!-- This templates was made by Colorlib (https://colorlib.com) -->
</html>
