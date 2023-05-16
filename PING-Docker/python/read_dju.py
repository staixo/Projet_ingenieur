import pandas as pd
import mysql.connector as mariadb

#connection to mariadb database
mariadb_connection = mariadb.connect(host='mariadb', port=3306, user='dalkia', password = 'esigelec', database='bdd')
cursor = mariadb_connection.cursor()

#read data from csv 
dju = pd.read_csv('./data/dju_mensuel.csv')

sql = "INSERT INTO degresjour_dju (dju_date, dju_valeur) VALUES (%s, %s)"

cursor.executemany(sql, dju.values.tolist())

mariadb_connection.commit()

mariadb_connection.close()

