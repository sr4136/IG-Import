# IG Import & Functionality

## Considerations & Caveats:
- This was created for my personal opinionated use case.
	- My preferences for date formats, default user, post content/block structure, etc are hard-coded.
	- If I were to build this for an audience, I would absolutely include options for customizing those.
	- I created a block theme, [SteveR Photography](https://github.com/sr4136/stever-photography), to work in tandem with this plugin. It relies on the "permalink" block that this plugin provides (at the page template level).
- This was developed using [Local](https://localwp.com/). 
- Data location related to each post:
	- Instagram "used to" only support one image per post and then enabled multiple images per post. Because of this, the data processing/validation/transformation checks both at the *root of the post* and then if not found, *each media item per post* for things like the post's title and timestamp.
- Media validation:
	- You'll see "initial media" vs "valid media" mentioned several times. This is due to the export containing posts that point to media that doesn't exist for some reason or another. Because my IG account was long since deleted, I cannot confirm exactly why these are missing from the export.

---

## Howto - Setup:
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
				- Caption - the "title" from the export, if provided. Else, defaults to `(untitled)`. This is passed to WP post content as a quote block.
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
	- This will run the import using the same processed data. 
	- One WP post will be created per IG post.
		- As above, the WP post's title, date, and content (blockquote, image block(s), and video block(s), as well as some wrapper group blocks) will be inserted as well as some classes based on the media counts.
		- The post author will default to the site's default author.
		- The posts will not contain any categories/tags other than "uncategorized."
2. Once complete, head over to your "All Posts" page of your site's wp-admin to find all of your imported posts.
