
# coding: utf-8
#import librairies
import pandas as pd
import numpy as np
import mysql.connector as mariadb

#connection to mariadb database
mariadb_connection = mariadb.connect(host='localhost', port=3307, user='dalkia', password = 'esigelec', database='bdd')
cursor = mariadb_connection.cursor()

#get back aggregate
sql = "SELECT SUM(R1), SUM(R2), site.site_client_id + 83, R1R2.annee FROM R1R2 INNER JOIN site ON R1R2.site_id = site.site_id GROUP BY site.site_client_id, R1R2.annee"
cursor.execute(sql)
r1r2 = cursor.fetchall()
mariadb_connection.close()

#build dataframe
aggregat = pd.DataFrame(r1r2, columns=["R1", "R2", "id", "annee"])

#connection to mariadb database
mariadb_connection = mariadb.connect(host='localhost', port=3307, user='dalkia', password = 'esigelec', database='bdd')
cursor = mariadb_connection.cursor()

#insert it on database
sql = "INSERT INTO R1R2(R1, R2, site_id, annee) VALUES (%s, %s, %s, %s)"
cursor.executemany(sql, aggregat.values.tolist())
mariadb_connection.commit()
mariadb_connection.close()

