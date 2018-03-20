# 
# This is a shell script that removes the patient access distribution
# file. This is a security step to reduce the chance of users 
# downloading an old version of the app, and/or overwriting the 
# current version with what is in an older distributoin file.
#

# Clean up the directory first.
# 
# Make sure we don't have an old tar dist file hanging around.
echo "";
echo "----    Removing old package     ----";
echo "";
rm access.tar;

# Remove all the backup files that end in a ~ char
echo "";
echo "----    Removing backup files    ----";
echo "";
find . -name "*~" -exec rm {} \; -print

echo "";
echo "----    Cleanup complete         ----";
echo "";
