This folder is a database migration tool.


As we develop the application the database might need to change.

Getting those changes to all users can be difficult.

This folder attempts to streamline the process.

If you go to this url, your database will be upgraded to the latest version.

http://127.0.0.1/translator_coordinator/migration


If you or your team need to update the database, you should follow these steps.
1. Add one to the value of $latest_version in index.php on the third line of the file.
2. Build a file names vX.php (X will be the new version number).
3. In that file, create a variable $sql 
4. Set $sql to a string of DDL statements needed to migrate the database to the correct state.
