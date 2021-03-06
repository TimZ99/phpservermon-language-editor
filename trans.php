<?php

/**
 * PHP Server Monitor Language Editor
 * Helps to maintain the translations files of PHP Server Monitor.
 *
 * This file is part of PHP Server Monitor Language Editor (PSMLE).
 * PHP Server Monitor Language Editor is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * PHP Server Monitor Language Editor is distributed in the hope
 * that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHP Server Monitor.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP versions 4, 5, 7 supported
 *
 * @category  PHP
 * @package   Phpservermon-Language-Editor
 * @author    Tim Zandbergen <Tim@Xervion.nl>
 * @copyright 2018 Tim Zandbergen
 * @license   http://www.gnu.org/licenses/gpl.txt GNU GPL v3
 * @version   GIT: 0.8.1
 * @link      http://www.github.com/TimZ99/phpservermon-language-editor/
 *
 * @todo menu to select the translation file
 * @todo Feature: export directly to Github
 **/

//debug
ini_set('display_errors', 1);
error_reporting(E_USER_ERROR);
//error_reporting(E_ALL);

if (!is_readable('./config.php')) {
    trigger_error("Config doesn't exist or is not readable.", E_USER_ERROR);
}
require './config.php';

/**
 * Get the names of the files in the directory.
 *
 * @var array|boolean
 */
$files = scandir($path);

/**
 * Disable input field if the input can not be saved.
 *
 * @var string
 */
$disable = '';

//check if path is directory
if ($files === false) {
    trigger_error("Path $path is not a directory.", E_USER_ERROR);
}
//check if default lang exists
if (!in_array('en_US.lang.php', /** @scrutinizer ignore-type */ $files)) {
    trigger_error("Default lang en_US not found.", E_USER_ERROR);
}
//check if translation file exists
if (!in_array($translationLang, /** @scrutinizer ignore-type */ $files)) {
    trigger_error("$translationLang not found.", E_USER_ERROR);
}
//check if default lang file is readable
if (!is_readable($path . '/en_US.lang.php')) {
    trigger_error("Default lang file not readable.", E_USER_ERROR);
}
//check if translation file is readable
if (!is_readable($path . "/" . $translationLang)) {
    trigger_error("$translationLang not readable.", E_USER_ERROR);
}

//get content of default lang
require $path . '/en_US.lang.php';
/**
 * Containing all default translations.
 * Default: en_US.
 *
 * @var array
 */
$default = $sm_lang;
unset($sm_lang);

//get content of translated lang
require $path . "/" . $translationLang;
/**
 * Containing all translations from translation file.
 *
 * @var array
 */
$translation = $sm_lang;
unset($sm_lang);

/**
 * Making sure the value won't break the file.
 *
 * @param string $value Value to be processed.
 *
 * @return string
 */
function processValue($value)
{
    $value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
    $value = preg_replace("/((\\')|(\'))/", "\'", $value);
    return $value;
}

/**
 * Set style for input field:
 * - if key doesn't exists in translation -> border red.
 * - if translation and default are the same -> border orange.
 * - else -> border default.
 *
 * @param string $key         Array key.
 * @param array  $translation Array containing translation.
 * @param string $value       Default translation.
 *
 * @return string
 */
function setStyle($key, $translation, $value)
{
    if (array_key_exists($key, $translation) && !empty($translation[$key])) {
        if (processValue($translation[$key]) == $value) {
            return 'border: 1px orange solid;';
        }
        return '';
    }
    return 'border: 1px red solid;';
}

/**
 * Return translation value.
 *
 * @param string $key         Array key.
 * @param array  $translation Array containing translation.
 *
 * @return string
 */
function translationValue($key, $translation)
{
    if (array_key_exists($key, $translation) && !empty($translation[$key])) {
        return processValue($translation[$key]);
    }
    return '';
}

/**
 * Display the default language and translation.
 *
 * @param array  $default     Array containing every default translation.
 * @param array  $translation Array containing every translation for the
 *                            translation file.
 * @param string $prevKey     Default ''. Otherwise previous key.
 * @param int    $px          Default 0. Left margin for input fields,
 *                            used for indentation.
 *
 * @return null
 */
function displayHTML(
    $default,
    $translation,
    $prevKey = '',
    $px = 0
) {
    global $disable;

    //foreach key check if value is an array -> run function again with that array
    //else echo input -> key, default, translation
    foreach ($default as $key => $value) {
        $key = processValue($key);

        if (is_array($value)) {
            echo "<input style=\"margin:5px 12px 0px " . $px . "px; width:15vw;\"
            tabindex=\"-1\" type=\"text\" value=\"$key\" $disable><br>\n\t";
            displayHTML($value, $translation[$key], $key, 24);
            continue;
        }

        $value = processValue($value);

        echo "<input style=\"margin:5px 12px 0px " . $px . "px; width:15vw;\"
        type=\"text\" tabindex=\"-1\" value=\"$key\" $disable>\n\t";
        echo "<input style=\"margin:5px 12px 0px 0px;\" type=\"text\"
        tabindex=\"-1\" value=\"$value\" $disable>\n\t";

        //if key is nested -> name is main|nested
        $name = ($prevKey != '') ? $prevKey . "|" . $key : $key;

        echo "<input style=\"margin:5px 0px 0px 0px; " .
            setStyle($key, $translation, $value) . "\" type=\"text\" 
            name=\"" . processValue($name) . "\"
            value=\"" . translationValue($key, $translation) . "\"
            $disable><br>\n\t";
    }
}

/**
 * Save translation.
 * Create array from input.
 *
 * @todo   add option to show output using textarea
 * @return string
 */
function saveTranslation()
{
    $array = array();
    foreach ($_POST as $key => $value) {
        if ($key == "submit") {
            continue;
        }
        if (empty($value)) {
            continue;
        }

        $containsSub = strpos($key, '|');

        if ($containsSub === false) {
            $array[$key] = $value;
            continue;
        }
        $main = substr($key, 0, $containsSub);
        $sub = substr($key, $containsSub + 1);
        array_key_exists($main, $array)
            ? $array[$main][$sub] = $value
            : $array[$main] = array($sub => $value);
    }
    $content = createContentForSave($array);
    return modifyFile($content);
}

/**
 * Change array to string.
 * Add license and documentation to content.
 * Make content ready to add to the translation file.
 *
 * @param array $array Contains processed input.
 *
 * @return string
 */
function createContentForSave($array)
{
    $content = "\$sm_lang = array(\n";
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $content .= str_repeat(' ', 4) . "'" . $key . "' => array(\n";
            foreach ($array[$key] as $nestedKey => $nestedValue) {
                $content .= str_repeat(' ', 8) . "'$nestedKey' => '" .
                wordwrap($nestedValue, (120 - strlen($nestedKey) - 9 - 8), "\n ") . "',\n";
            }
            $content .= str_repeat(' ', 4) . "),\n";
            continue;
        }
        $content .= str_repeat(' ', 4) . "'" . $key . "' => '$value',\n";
    }
    $content .= ");\n";
    return $content;
}

/**
 * Load changes to translation file.
 * Copy documentation and past the new translation under it.
 *
 * @param string $newContent New content for file. Default set to prevent errors.
 *
 * @return string
 */
function modifyFile($newContent = '')
{
    global $path;
    global $translationLang;
    $currentContent = file_get_contents($path . "/" . $translationLang);
    $licenseAndDocs = substr(
        $currentContent,
        0,
        strpos($currentContent, '$sm_lang')
    );
    file_put_contents($path . "/" . $translationLang, $licenseAndDocs . $newContent);
    return "Success";
}

/**
 * Check if file is writable. (Currently required to save.)
 * Check if $_POST["submit"] isset.
 *
 * @return string
 */
function checkForSave()
{
    global $path;
    global $translationLang;
    global $disable;
    $message = false;
    $messageBox = "<div style=\"width:50vw; margin-left:25vw; padding:12px; 
        border:black 1px solid; border-radius:5px;\">";

    if (!is_writable($path . "/" . $translationLang)) {
        $message = true;
        $messageBox .= "<b style=\"color:red;\">File not writable.</b><br>
            Permission: "
            . substr(sprintf('%o', fileperms($path . "/" . $translationLang)), -4)
            . ".<br>Should be 0666.<br>Unix: chmod 0666 $path/$translationLang";
        $disable = "disabled";
    }
    if (isset($_POST["submit"])) {
        $message = true;
        $messageBox .= saveTranslation();
    }

    $messageBox .= "</div><br><br>";
    return $message ? $messageBox : '';
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
        PSMLE - <?php echo $translationLang; ?>
        </title>
        <style>
            html, body{
                overflow-x:hidden;
                font-family: system-ui;
                margin: 0px 0px 0px 1vw;
            }
            input{width:30vw;} 
            ul{list-style-type: circle;}
            button{
                width: 49vw; 
                height: 50px;
                border-radius: 10px;
                background-color: rgb(75, 205, 20);
                border: black 1px solid;
                color: white; 
                font-size: 15px;
                margin: 24px 0px 24px 24vw;
            }
            button:active{background-color: rgb(43, 117, 12);}
            button:hover{background-color: rgb(61, 166, 16);}

        </style>
        <!--[if lt IE 9]>
            <script src="/js/html5shiv.js"></script>
        <![endif]-->
    </head>
    <body>
        <ul>
            <li>Red: no translation found.</li>
            <li>Orange: possibly not translated.</li>
        </ul>
        <br>
        <b>
            DON'T FORGET TO SAVE!
            <br><br>
            If you want to change the default translation, 
            select it as the translation file.
        </b>
        <br><br>
        <?php
        echo checkForSave();
        ?>
        <input style="margin:5px 12px 0px 0px; width:15vw;" value="Key"
            tabindex="-1" readonly>
        <input style="margin:5px 12px 0px 0px;" value="Default"
            tabindex="-1" readonly>
        <input style="margin:5px 12px 0px 0px;" value="Translation"
            tabindex="-1" readonly>
        <br><br><br>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <?php
        displayHTML($default, $translation);
        ?>
        <button type="submit" name="submit" value="1">Save translation</button>
        </form>
    </body>
</html> </form>
    </body>
</html>