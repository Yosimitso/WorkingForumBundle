Here's all bundle's parameters,
please note that array nodes (such as 'vote') **are required and must be defined**

yosimitso_working_forum_bundle:

| Subnode    | Parameters                    | Required | Accepted                  | Default     | Explanations |
|------------|-------------------------------|----------|---------------------------|-------------|--------------|
|            | site_title                    | **Yes**  | String not empty          |             | Site's title, used in email |
|            | allow_anonymous_read          | **Yes**  | Boolean                   |             | Allow or not access to anonymous user (in both cases, anonymous can't post)|
|            | thread_per_page               | No       | Integer > 0               | 50          | Number of threads displayed per page (pagination related) |
|            | post_per_page                 | No       | Integer > 0               | 20          | Number of posts displayed per page (pagination related)  |
|            | date_format                   | No       | String, valid date format | d/m/Y H:i:s | Date and time with PHP format, used for rendering|
|            | allow_moderator_delete_thread | No       | Boolean                   | false       | Allow or not moderators to delete threads |
|            | theme_color                   | No       | Among : blue, green       | green       | Theme color |
|            | lock_thread_older_than        | No       | Integer (0 = disabled)    | 365           |  Days between the last thread's post and the autolocking of the thread, 0 means disabled |
|            | post_flood_sec                | No       | Integer > 0               | 30          | seconds minimum between each post for an user |
|vote:|
|            |threshold_useful_post          | No       | Integer > 0               | 5           | Number of votes needed for a post to be considered as useful |
|file_upload:|
|            | enable                        | **Yes**  | Boolean                   |             | Allow or not users to upload enclosed files |
|            | max_size_ko                   | No       | Integer > 0               | 10000       | Files size max per post, remember to check if this value is not greater than directives into your php.ini |
|            | accepted_format               | No       | Array                     | [image/jpg, image/jpeg, image/png, image/gif, image/tiff, application/pdf] | Accepted file format |
|            | preview_file                  | No       | Boolean                   | true        | For images only, display or not the thumbnail |
|thread_subscription:|
|            | enable                        | **Yes**  | Boolean                   | false       | Allow or not thread's subscription, remember to check your swiftmailer configuration
