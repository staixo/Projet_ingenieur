import pandas as pd
import mysql.connector as mariadb
import numpy as np

#connection to mariadb database
mariadb_connection = mariadb.connect(host='mariadb', port=3306, user='dalkia', password = 'esigelec', database='bdd')
cursor = mariadb_connection.cursor()

#read excel sheet
seco = pd.read_excel(r'./data/consos mensuelles et DJU.xlsx', sheet_name='secomile-M3')
#add id_site
seco['id_site'] = np.repeat(17, len(seco.index)).tolist()
seco = seco[["id_site", "date", "kwh"]]

#add to database
#insert it on database
sql = "INSERT INTO consommation(consommation_site_id, consommation_date_conso, kwh) VALUES (%s, %s, %s)"

cursor.executemany(sql, seco.values.tolist())

mariadb_connection.commit()

mariadb_connection.close()