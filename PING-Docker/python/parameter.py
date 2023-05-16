import pandas as pd
import numpy as np
import mysql.connector as mariadb

#connection to mariadb database
mariadb_connection = mariadb.connect(host='mariadb', port=3306, user='dalkia', password = 'esigelec', database='bdd')
cursor = mariadb_connection.cursor()

liste = []
#make combinations
for meteo in ["Stable", "Froide", "Chaude"]:
    for puissance in [0.5, 0.6, 0.7, 0.8, 0.9, 1, 1.1, 1.2, 1.3, 1.4, 1.5]:
        for isolation in [0, 0.1, 0.2, 0.3, 0.4, 0.5]:
            liste.append([meteo, puissance, isolation])

#insert it on database
sql = "INSERT INTO parametre(scenario_meteo, puissance_souscrite, pourcentage_reduction_isolation) VALUES (%s, %s, %s)"

cursor.executemany(sql, liste)

mariadb_connection.commit()

mariadb_connection.close()