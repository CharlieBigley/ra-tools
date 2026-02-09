# Agent documentauion guidelines

## Terminology

**Article:** In all documentation and instructions, "Article" refers specifically to the textual content of an article held in the documentation database given below (not the full API response or metadata).

## Updating Joomla Documentation

**Introtext Rule:**
When creating or updating an Article, if the text content is 250 characters or fewer, use the full text as the introtext field. If the text exceeds 250 characters, generate a summary (such as the first sentence or the first 200 characters, ending at a word boundary) and use this as introtext.

**Default Category:**
If no category is specified when creating an Article, use "Internal" as the default category.

Extensive documentation exists as Articles on the website: https://docs.stokeandnewcastleramblers.org.uk/.


If instructed to update a particular Article, the required category and alias will be provided. The agent should immediately follow these steps, without further prompting:


1. Use the standard Joomla API to download the specified Article and store it in a new temporary file. When making API requests, always include the API key as a Bearer token in the Authorization header.
2. After downloading, extract the article text from the JSON response by locating the correct article in the `data` array (matching the alias and category if multiple are returned), then reading the `attributes` → `text` field. This is the main article content. Save this extracted text to a tempory file.
3. Open the temporary file ina Code tab, and display the extracted article content to the user to edit. Then stand by for further instructions.
4. After editing, if requested, use the Joomla API to push the updated Article back to the website, overwriting the previous version.

**API Access:**  
To access the site, use the following API key (keep this secure):  
`c2hhMjU2Ojk3OTo4NDAwYjAzNDA0OTFmNjlkNTlhMGQ3NWQ4MDA0MTRmNmQ0ZDgyMjAxZmE5ZGI5NmU0YzEzMzFhNjhmOWM1MTRi`


**API Request Example:**
Always include the API key as a Bearer token in the Authorization header. When using filter parameters with square brackets, ensure they are URL-encoded. For example, using curl:

```
curl -H "Authorization: Bearer <API_KEY>" \
   "https://docs.stokeandnewcastleramblers.org.uk/api/index.php/v1/content/articles?filter%5Balias%5D=<ALIAS>&filter%5Bcategory%5D=<CATEGORY>"
```
Replace `<API_KEY>`, `<ALIAS>`, and `<CATEGORY>` with the actual values.

**Extracting Article Content:**
After downloading the JSON response, parse it and extract the article text from the `attributes` → `text` field of the correct article object in the `data` array. This matches the logic in `remoteitem.php`.

**Article Formatting:**  
No special styling should be included in the Article, but standard styles (such as headings H1, H2, etc.) are available.

**Creating New Articles:**  
If instructed to write a new Article, open a blank Code window for the User to provide content. A subsequent instruction to save the document may or may not be given. If it is, push the finished document to the website.  

If pushing is requested, the agent will prompt for:
- Article name
- Required alias
- Required category
then create a new Article.

# Agent Installation and Compatibility Guidelines

## Documentation Note

When generating a directory tree or folder structure for documentation, include only the folder hierarchy by default. Do not list files within folders unless explicitly requested.

## Purpose

This file provides mandatory steps for any agent, developer, or automated tool before installing or upgrading software in this environment. It ensures compatibility and system stability.

## Compatibility Checklist

1. **Check Operating System Version:**
   - macOS: `sw_vers`
   - Linux: `lsb_release -a` or `cat /etc/os-release`
2. **Check Existing Software Versions:**
   - PHP: `php -v`
   - MySQL: `mysql --version` (or check via phpMyAdmin/Joomla System Information)
   - Apache: `apachectl -v` or `httpd -v`
3. **Check Package Information:**
   - Homebrew: `brew info <package>`
   - apt: `apt show <package>`
4. **Consult Official Documentation:**
   - Confirm compatibility between MySQL, PHP, Apache, and your OS version.
   - Search for known issues with your OS and the software version you plan to install.
5. **Backup Important Data:**
   - Backup databases and configuration files before making changes.
6. **After Installation:**
   - Verify the service is running and accessible.
   - Check that Joomla and phpMyAdmin still function as expected.

## Notes
- If unsure, test installations/upgrades in a virtual machine or container first.
- Always follow this checklist before any installation or upgrade.
