# 
# setup.sh
#
# The setup script will test and install a skeleton 
# WebApp.

# First remove the CVS references, so that this cannot 
# be checked in after it has been modified.
find . -name CVS -exec rm -r {} \; -print

# Create the Database for the skeleton project. 
# We can do this by prompting for the DB name, and 
# loading a data dump from the development skeleton.
#
# Prompt for the MySQL host  (joplin.bio.ri.ccf.org)

# Prompt for the MySQL username with create db priv
# (jehrling)


# Prompt for the MySQL Database Name you want to create
# (WebApp)

# Create the DB (show why we ask for password)
echo "Please enter the MySQL password for $username, so we can create the $DB_name Database."
mysql -h $hostname -u $username -p -e "Create Database $DB_name;"

# Load the db schema.
echo "Please enter the MySQL password for $username again, so we can import the DB schema."
mysql -h $hostname -u $username -p $DB_name -e "source ./schema.sql"


# I'd like to prompt for the application and site 
# parameters and automatically change the references in
# application.php

# Then we need to remove the setup files.
