require 'compass/import-once/activate'
# Require any additional compass plugins here.

# Set this to the root of your project when deployed:
http_path = "/"
css_dir = "frontend/web/css"
sass_dir = "frontend/web/scss"
images_dir = "frontend/web/img"

generated_images_dir = "frontend/web/img/sprites"
generated_images_path = "frontend/web/img/sprites"

http_generated_images_dir = "/img/sprites"
http_generated_images_path = "/img/sprites"

# You can select your preferred output style here (can be overridden via the command line):
# output_style = : or :nested or :compact or :compressed
output_style = :expanded

# To enable relative paths to assets via compass helper functions. Uncomment:
# relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
line_comments = true

# If you prefer the indented syntax, you might want to regenerate this
# project again passing --syntax sass, or you can uncomment this:
# preferred_syntax = :sass
# and then run:
# sass-convert -R --from scss --to sass sass scss && rm -rf sass && mv scss sass\

encoding = "utf-8"
Encoding.default_external = 'utf-8'