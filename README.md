# IG Import & Functionality

The result of this work can be seen on my [photo blog](https://steverudolfi.com/photography/).

## Considerations & Caveats:
- Improvements:
	- I'd like to use the [ActivityPub](https://wordpress.org/plugins/activitypub/) plugin to connect this to the fediverse.
	- Could & should this be broken into multiple plugins-- one for the import functionality and one for the block & styles? Probably. Definitely.
	- It'd be a better idea to use JavaScript or AJAX to execute the import request considering server timeouts vs size of import.
	- Along with the above, it'd be nice to implement per-post import status messages, ex "post xyz created successfully."
	- This was written to run once and completely. I would write some checks for existing media & posts-- in order to prevent duplication in the event of partial imports or accidental re-runs.
- This was created for my personal opinionated use case.
	- My preferences for date formats, default user, post content/block structure, etc are hard-coded.
	- If I were to build this for an audience, I would absolutely include options for customizing those.
	- I created a block theme, [SteveR Photography](https://github.com/sr4136/stever-photography), to work in tandem with this plugin. It relies on the "permalink" block that this plugin provides (at the page template level).
- This was developed using [Local](https://localwp.com/). 
- Data location related to each post:
	- Instagram "used to" only support one image per post and then enabled multiple images per post. Because of this, the data processing/validation/transformation checks both at the *root of the post* and then if not found, *each media item per post* for things like the post's title and timestamp.
- Media validation:
	- You'll see "initial media" vs "valid media" mentioned several times. This is due to the export archive containing posts that point to media that doesn't exist for some reason or another. Because my IG account was long since deleted, I cannot confirm exactly why these are missing from the export archive.

## Permalink Block
- This was created as a wrapper to hold the post-content block within the query block in block templates ([index](https://github.com/sr4136/stever-photography/blob/8bcd7ee087b1a44f717fe56cc79104a6babc84c6/templates/index.html#L16-L19C1) & [archive](https://github.com/sr4136/stever-photography/blob/8bcd7ee087b1a44f717fe56cc79104a6babc84c6/templates/archive.html#L11-L15)). With each post styled as a "card" or "polaroid," the post content (blockquote, image(s), video(s)) would be wrapped in the permalink. The post-date block is also linked to the individual post with its settings. The categories/tags that output within the card are links to each term's archive page. 
	- Improvements:
		- There are accessibility considerations that I'd like to circle back to including but not limited to the title attribute for the permalink block.
		- The video blocks have controls enabled. I'd like to disable those controls when viewing the index/archive templates.
- Architecture notes:
	- block.json
		- the `editorStyle` property is set to `index.css`, compiled from `editor.scss`.
		- the `style` property is omitted here, as I am also using it for some theme styles and enqueueing it via the [plugin's entry point file](https://github.com/sr4136/IG-Import/blob/main/igtestimport.php#L95-L101).
	- A [dynamic block renderer](https://github.com/sr4136/IG-Import/blob/main/igtestimport.php#L37-L46), but [save.js](https://github.com/sr4136/IG-Import/blob/main/src/save.js) was still required to store the innerblocks.

---

## Data Parsing & Import Process:
1. Get the JSON file provided by the Instagram export and parse it into a data structure.
	- https://github.com/sr4136/IG-Import/blob/main/data_process.php
2. Output the data structure and a visual representation of that data-- for verification.
 	- https://github.com/sr4136/IG-Import/blob/main/data_output_test.php
3. Run the import:
	- Loop through the data structure and [prepare the new post's content](https://github.com/sr4136/IG-Import/blob/main/data_run_import.php#L42-L75) with the desired block markup.
	- [Sideload the media](https://github.com/sr4136/IG-Import/blob/main/data_run_import.php#L2-L30) into the library.
	- [Publish the post](https://github.com/sr4136/IG-Import/blob/main/data_run_import.php#L78-L88).

---

## Howto - Setup for Import:
1. Download JSON archive via IG: [https://www.instagram.com/download/request/](https://www.instagram.com/download/request/).
2. Install this plugin.
3. Create a directory within the root of this plugin called `ig_data`.
4. Navigate within your instagram archive .zip file.
	- Copy your `media/posts` directory (with all of the numerically named subdirectories) into the directory you created in step 3. 
		- Ex: `ig_data/media/posts/#####` etc.
	- Copy `content/posts_1.json` into the directory you created in step 3.
		- Ex: `ig_data/posts_1.json`.
5. On your site, navigate to `wp-admin` and then Settings > Insta Import.
6. On this page, you'll see two buttons: "Test Data" and "Run Import".
	- ![Screenshot: IG Import Buttons](https://user-images.githubusercontent.com/4681620/236632082-52190ff4-03eb-42f7-91ad-fcbc1db1e4b8.png)


## Howto - Test Data:
1. Click the green "Test Data" button.
	- This will 1) process, 2) validate, and 3) transform the data you supplied in the Setup phase.
	- The page will reload and you'll be presented with a few things:
		1. A success message:
			- Ex: `Success: JSON file found at "Path\Local Sites\igtest\app\public\wp-content\plugins\igtestimport\ig_data\posts_1.json"`
		2. The "Processed data" in an expandable textarea, including:
			- `post_count_initial` - The number of posts that were found within `posts_1.json`. 
			- `post_count_valid` - The number of valid posts after processing. This removes posts where the image/video media cannot be found or otherwise had errors. 
			- `posts` - This is an array containing each of the posts and their corresponding processed data.
				- Title - the title based on standardized text "IG Import" followed by the date formatted for display. This is what is passed to WP for the post's title.
				- Dates - in several formats:
					- unformatted - UNIX timestamp provided by the export.
					- display - my preferred display format, for things like the title above.
					- for WP - this is the format passed to WP for the post's creation date.
				- Caption - the "title" from the export, if provided. Else, defaults to `(untitled)`. This is passed to WP post content as a 
				block.
				- Media counts:
					- Initial/valid media count - How many media items the export had for each post vs how many were found existing.
					- Valid images/video count - individually, how many valid images/videos per post were found.
					- Diff - the calculation of initial count minus valid.
				- Has mixed media - a flag for whether the post has both images and video.
				- Media - URLS to the media files. These are passed to WP post content as image/video blocks.
			- ![Screenshot: Processed Data](https://user-images.githubusercontent.com/4681620/236633882-befd08a6-2b20-4870-8495-c81628704166.png)
		3. A repeat of the Initial and Valid post counts. 
		4. The "Posts" processed visual data in an expandable area. This includes more readable/scannable data plus thumbnails of the valid media.
			- ![Screenshot: Visual Data](https://user-images.githubusercontent.com/4681620/236633963-b0aa6d21-1ff7-476b-9067-e67e97acdd60.png)

## Howto - Import Data to Posts:
1. Click the yellow "Run Import" button.
	- ⚠️ ***This will take some time.***
	- This will run the import using the same processed data. 
	- One WP post will be created per IG post.
		- As above, the WP post's title, date, and content (blockquote, image block(s), and video block(s), as well as some wrapper group blocks) will be inserted as well as some classes based on the media counts.
		- The post author will default to the site's default author.
		- The posts will not contain any categories/tags other than "uncategorized."
2. Once complete, head over to your "All Posts" page of your site's wp-admin to find all of your imported posts.
	- Depending on the theme you've got activated, your experience (viewing the posts on the frontend of your site) may vary. 
