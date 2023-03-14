# Media Files

## Filebrowser

Query Params
* config: Media config name
* path: Urlencoded relative directory path. e.g. 'path/to/dir'
* file: Urlencoded file name including extension. e.g. 'filename.ext'

Directory Actions:
* View
* Rename
* Copy (todo)
* Move (todo)

File Actions:
* View (depends on file extension / mimetype)
* Edit (depends on file extension / mimetype)
* Rename
* Copy (todo)
* Move (todo)

FileBrowser helper methods:
* currentWorkingDir
* currentWorkingFile
* currentDirPath
* currentDirName
* currentFilePath
* currentFileName

* currentFile
* currentFolder


### View cells

* DirectoryListCell
  * Args:
    * ?
  * Options:
    * MediaManager / Filesystem instance
    * Show hidden folders
    * Show folders
    * Show files
    * Folder actions
    * File actions
  * Renderer:
    * Table (default)

## Media File

* filename
* size
* path
* mimetype
* url?


```json
{
    "config": "default",
    "path": "files\/products\/read-more\/2015-02-12 Kurzbeschreibung CAPTain advanced.pdf",
    "basename": "2015-02-12 Kurzbeschreibung CAPTain advanced.pdf",
    "url": "http:\/\/localhost:8300\/media\/files\/products\/read-more\/2015-02-12 Kurzbeschreibung CAPTain advanced.pdf"
}
```