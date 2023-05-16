import pandas as pd
import mysql.connector as mariadb

#connection to mariadb database
mariadb_connection = mariadb.connect(host='mariadb', port=3306, user='dalkia', password = 'esigelec', database='bdd')
cursor = mariadb_connection.cursor()

#read data from csv 
r1r2 = pd.read_csv('./data/r1r2.csv')

#get site table back from the database in order to have the ids of the sites
sql = "SELECT site_id, nom FROM site"
cursor.execute(sql)
sites = cursor.fetchall()
mariadb_connection.close()

df_sites = pd.DataFrame(sites, columns=["id_site", "nom_site"])

#merge the site table and the r1r2 from the csv
table = r1r2.merge(df_sites, how='inner', left_on='SITES', right_on='nom_site')

#just keep the columns of the table structure in the database
table.drop(['SITES', 'nom_site'], axis=1, inplace=True)

#put column in the right order
table = table[['id_site', 'Year', 'R1', 'R2']]

#connection to mariadb database
mariadb_connection = mariadb.connect(host='mariadb', port=3306, user='dalkia', password = 'esigelec', database='bdd')
cursor = mariadb_connection.cursor()

#insert the values in the database
sql = "INSERT INTO R1R2(site_id, annee, R1, R2) VALUES (%s, %s, %s, %s)"

cursor.executemany(sql, table.where(table.notnull(), None).values.tolist())

mariadb_connection.commit()

mariadb_connection.close()