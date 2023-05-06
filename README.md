# IG Import & Functionality

### Dev Bookmarks:
- https://developer.wordpress.org/rest-api/using-the-rest-api/backbone-javascript-client/

### Howto: 
1. Download JSON archive via: https://github.com/vintagesucks/instagram-export
2. Download HTML archive via IG: https://www.instagram.com/download/request/
3. Move all images/videos to a folder called "ig_media".
    - Remove extra digits at the end of the filenames with `(n_\d*)`. Ex: https://regexr.com/79qbh.
        - Used Windows PowerToys PowerRename.
4. Spin up a test site using Local. 
5. Install this plugin.
6. Move the "ig_media" folder to the root of the site.
7. 
