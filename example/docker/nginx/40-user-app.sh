#!/bin/bash

USER_NAME=app

cat /etc/passwd | grep ${USER_NAME} >/dev/null 2>&1
if [ $? -eq 0 ] ; then
    echo "user ${USER_NAME} exists"
else
    groupadd -r ${USER_NAME} -g 1000 && useradd -u 1000 -r -g ${USER_NAME} -m -d /home/${USER_NAME} -s /bin/bash -c "App user" ${USER_NAME}
fi
