# Deployment

This document describes current deployment of the process.

         +-----------------------+
         | *GitHub repository*   |
         | repli2dev/scienceslam |
         +----------+------------+
                    |
    +---------------X-----------------+
    |               |                 |
    |    +----------v------------+    |
    |    | *Web*                 |    |
    |    | webserver.ics.muni.cz |    |
    |    | user: scienceslam     |    |
    |    +----------+------------+    |
    |               |                 |
    |    +----------v------------+    |
    |    | *MySQL database*      |    |
    |    | elrond.ics.muni.cz    |    |
    |    | user: scienceslam     |    |
    |    +-----------------------+    |
    |                                 |
    |    Síť MUNI                     |
    |                                 |
    +---------------------------------+

The webserver and MySQL server are inside MUNI network and has to be accessed from the MUNI network.

The current version is developed in `master` in `repli2dev/scienceslam` repository on Github (for deployment instructions see [README.md](README.md) file.

## Backups

The web and mysql servers are backuped three times a week on tapes.

For backup responsivity manual week backups of database are advised, for crontab entry see [DEVELOPING.md](DEVELOPING.md) file.