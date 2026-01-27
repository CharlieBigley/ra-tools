<?php
/**
 * @version    5.3.2
 * @package    com_ra_tools
 * @author     Charlie Bigley <webmaster@bigley.me.uk>
 * @copyright  2024 Charlie Bigley
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * 24/01/26 Created by copilot
 * 19/09/26 CB corrected build logic
 */
namespace Ramblers\Component\Ra_tools\Site\Model;

use \Joomla\CMS\MVC\Model\FormModel;

/**
 * Build Model
 * Handles the component building logic
 */
class BuildModel extends FormModel
{
     public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_ra_tools.build', 'build', array(
            'control' => 'jform',
            'load_data' => $loadData
                )
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }
    /**
     * Build the component package
     */
    public function build($component, $version)
    {
        ob_start();
        
        try {
            $result = $this->doBuild($component, $version);
            $output = ob_get_clean();
            
            return [
                'success' => $result,
                'output' => $output,
                'zipFile' => $result ? "$component-$version.zip" : null,
                'component' => $component,
                'version' => $version
            ];
        } catch (\Exception $e) {
            $output = ob_get_clean();
            return [
                'success' => false,
                'output' => $output . "\n\nException: " . $e->getMessage(),
                'component' => $component,
                'version' => $version
            ];
        }
    }

    /**
     * Perform the actual build process
     */
    private function doBuild($component, $version)
    {
        // Get the ra-tools root directory (6 levels up: site/src/Model from ra-tools root)
        $scriptDir = dirname(dirname(dirname(dirname(__FILE__))));
        $componentDir = $scriptDir . '/' . $component;
        
        // Validate component directory exists
        if (!is_dir($componentDir)) {
            echo "Error: Component directory not found: $componentDir\n";
            return false;
        }
        
        // Extract the manifest filename from component name (com_ra_tools -> ra_tools)
        $manifestName = preg_replace('/^com_/', '', $component);
        
        echo "Starting build for component: $component, version: $version\n";
        echo "Script directory: $scriptDir\n";
        echo "Component directory: $componentDir\n";
        echo "Manifest name: $manifestName\n";
        die(); 
        // Change to component directory
        if (!chdir($componentDir)) {
            echo "Error: Could not change to directory: $componentDir\n";
            return false;
        }
        
        echo "Building $component-$version.zip...\n";
        
        // Step 1: Copy manifest to correct location (dereference symlink)
        echo "  - Copying manifest file...\n";
        $sourceManifest = 'administrator/' . $manifestName . '.xml';
        $destManifest = $manifestName . '.xml';
        
        if (!file_exists($sourceManifest)) {
            echo "Error: Source manifest file not found: $sourceManifest\n";
            return false;
        }
        
        if (!copy($sourceManifest, $destManifest)) {
            echo "Error: Failed to copy manifest file\n";
            return false;
        }
        
        // Step 2: Create zip with required folders, manifest, and script
        echo "  - Compressing files...\n";
        
        $zipFile = "$component-$version.zip";
        $zip = new \ZipArchive();
        
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            echo "Error: Could not create zip file\n";
            if (file_exists($destManifest)) {
                unlink($destManifest);
            }
            return false;
        }
        
        // Directories to include in the zip - discover dynamically
        $dirsToInclude = [];
        $excludeDirs = ['.', '..', '.git', '.gitignore', '.DS_Store'];
        
        // Scan for all directories in the component folder
        $items = scandir('.');
        foreach ($items as $item) {
            if (is_dir($item) && !in_array($item, $excludeDirs) && strpos($item, '.') !== 0) {
                $dirsToInclude[] = $item;
            }
        }
        
        $filesToInclude = ['script.php', $destManifest];
        
        // Add files
        foreach ($filesToInclude as $file) {
            if (file_exists($file)) {
                $zip->addFile($file, $file);
            }
        }
        
        // Add directories recursively
        foreach ($dirsToInclude as $dir) {
            if (is_dir($dir)) {
                $this->addDirToZip($zip, $dir, $dir);
            }
        }
        
        $zip->close();
        
        // Step 3: Verify zip was created
        if (file_exists($zipFile)) {
            echo "✓ Package created successfully: $zipFile\n";
            
            // Step 4: Delete the temporary manifest copy
            echo "  - Cleaning up temporary files...\n";
            if (!unlink($destManifest)) {
                echo "Warning: Failed to delete temporary manifest file\n";
            }
            
            echo "✓ Build complete\n";
            return true;
        } else {
            echo "✗ Error: Failed to create zip file\n";
            if (file_exists($destManifest)) {
                unlink($destManifest);
            }
            return false;
        }
    }

    /**
     * Recursively add a directory to a ZipArchive
     */
    private function addDirToZip($zip, $dir, $zipPath = '')
    {
        $excludePatterns = ['.DS_Store', '.git'];
        
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            // Check if file should be excluded
            $shouldExclude = false;
            foreach ($excludePatterns as $pattern) {
                if (strpos($file, $pattern) !== false) {
                    $shouldExclude = true;
                    break;
                }
            }
            
            if ($shouldExclude) {
                continue;
            }
            
            $filePath = $dir . '/' . $file;
            $zipFilePath = $zipPath ? $zipPath . '/' . $file : $file;
            
            if (is_dir($filePath)) {
                $this->addDirToZip($zip, $filePath, $zipFilePath);
            } else {
                $zip->addFile($filePath, $zipFilePath);
            }
        }
    }
}
?>
