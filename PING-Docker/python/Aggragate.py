
# coding: utf-8
#importing librairies
import pandas as pd
import numpy as np
import mysql.connector as mariadb



#connection to mariadb database
mariadb_connection = mariadb.connect(host='localhost', port=3307, user='dalkia', password = 'esigelec', database='bdd')
cursor = mariadb_connection.cursor()

#get consommation from database
sql = "SELECT SUM(kwh), consommation_date_conso, site.site_client_id  FROM consommation  INNER JOIN site ON consommation.consommation_site_id = site.site_id  GROUP BY site_client_id, consommation_date_conso"
cursor.execute(sql)
sites = cursor.fetchall()

conso = pd.DataFrame(sites, columns=["kwh", "date", "client_id"])

cursor.close()

cursor = mariadb_connection.cursor()

sql = "SELECT * FROM client"
cursor.execute(sql)
sites = cursor.fetchall()

client = pd.DataFrame(sites, columns=["id", "nom"])

cursor = mariadb_connection.cursor()

sql = "SELECT MAX(site_id) FROM site"
cursor.execute(sql)
sites = cursor.fetchall()

max_id = sites[0][0]

conso.client_id = conso.client_id.apply(lambda x: x + max_id)
conso.columns = ["kwh", "date", "id_site"]


client["site_id"] = client.id.apply(lambda x: x + max_id)

client.nom = client.nom.apply(lambda x: x + " GLOBAL")

## Insert into the database


cursor = mariadb_connection.cursor()
sql = "INSERT INTO site (site_client_id, nom, site_id) VALUES (%s, %s, %s)"
cursor.executemany(sql, client.values.tolist())
mariadb_connection.commit()

cursor = mariadb_connection.cursor()
sql = "INSERT INTO consommation (kwh, consommation_date_conso, consommation_site_id) VALUES (%s, %s, %s)"
cursor.executemany(sql, conso.values.tolist())
mariadb_connection.commit()


mariadb_connection.close()

