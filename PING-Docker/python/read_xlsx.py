import pandas as pd
import numpy as np
from fuzzywuzzy import fuzz
from fuzzywuzzy import process
import requests
import mysql.connector as mariadb

#Read consommation

##2017
###read
conso_2017 = pd.read_excel(r"./data/consos mensuelles et DJU.xlsx", sheet_name="conso mensuelles 2017")
###clean the data
### drop useless columns
columns_2017_to_drop = ['Unité', 'Cumul', 'Commentaires', 'Unnamed: 16']
conso_2017.drop(columns_2017_to_drop, axis=1, inplace=True)
#### set the index
conso_2017.set_index('SITES', inplace=True)
#### convert the data type of the columns
conso_2017.columns = pd.to_datetime(conso_2017.columns)conso_2017.reset_index(inplace=True)
conso_2017.reset_index(inplace=True)

##2016
conso_2016 = pd.read_excel(r"./data/consos mensuelles et DJU.xlsx", sheet_name="2016")

##2015 : 2 parts
conso_2015_1 = pd.read_excel(r"./data/consos mensuelles et DJU.xlsx", sheet_name="2015 1ere période", header=3)
conso_2015_2 = pd.read_excel(r"./data/consos mensuelles et DJU.xlsx", sheet_name="2015 2eme periode", header=3)
###Merge two parts with fuzzywuzzy librairy
conso_2015_1['key'] = conso_2015_1['Étiquettes de lignes'].apply(lambda x: [process.extract(x, conso_2015_2['Étiquettes de lignes'], limit=1)][0][0][0])
conso_2015 = conso_2015_1.merge(right=conso_2015_2, left_on='key', right_on='Étiquettes de lignes', how='inner')
conso_2015.drop_duplicates('Étiquettes de lignes_y', inplace=True, keep='first')

## Merge years
conso_2017['key'] = conso_2017.SITES.apply(lambda x: [process.extract(x, conso_2016['Unnamed: 0'], limit=1)][0][0][0])
df = conso_2017.merge(conso_2016,left_on='key',right_on='Unnamed: 0')
conso_2015['key'] = conso_2015['Étiquettes de lignes_y'].apply(lambda x: [process.extract(x, df['SITES'], limit=1)][0][0][0])
total = conso_2015.merge(df, left_on='key', right_on='SITES')
### because some match are false we need to fix it
### to do it we will calculate the ratio of simularity between names and delete ones below 47
list_of_index_to_drop = []
for index, row in total[['SITES', 'Étiquettes de lignes_y']].iterrows(): 
    if fuzz.ratio(row['SITES'], row['Étiquettes de lignes_y']) < 47:
        list_of_index_to_drop.append(index)
total.drop(list_of_index_to_drop, inplace=True)
### drop useless columns
columns_to_drop = ['Étiquettes de lignes_x', '(vide)_x', 'Total général_x', 'Étiquettes de lignes_y', 'key_x', '(vide)_y',
                  'Unnamed: 0', 'key_y', 'Total général', 'Total général_y']

total.drop(columns_to_drop, axis=1, inplace=True)
total.set_index('SITES', inplace=True)
#create a copy of the dataframe
trial = total.copy()
#drop duplicated index and keep just the first one
trial = trial[~trial.index.duplicated(keep='first')]
#create the sparse dataframe
df_final = pd.DataFrame(columns=['SITES', 'DATE', 'KWH'])
#insert each value in the sparse dataframe
id_ = 1
for i in list(trial.index):
    for j in list(trial.columns):
        df_final.loc[id_] = [i, j, trial.loc[i][j]]
        id_ = id_ + 1
#convert columns to the appropriate type
df_final['SITES'] = df_final['SITES'].apply(lambda x: str(x))
df_final['DATE'] = pd.to_datetime(df_final['DATE'])
df_final.fillna(value=0, inplace=True)
df_final['KWH'] = pd.to_numeric(df_final['KWH'], errors='raise')
#insert month and year columns
df_final['Month'] = np.repeat(np.NaN, len(df_final.index))
df_final['Year'] = np.repeat(np.NaN, len(df_final.index))
#extracting month and year from the date and insert it into the dataframe
df_final['Month'] = df_final['DATE'].apply(lambda x : x.month)
df_final['Year'] = df_final['DATE'].apply(lambda x : x.year)
#Sum consommation by year and month for each site
table = df_final.groupby(['SITES', 'Month', 'Year']).sum().reset_index()
#put the date in the appropiate format for the sql table (always the 15th of the month)
table['date'] = table['Year'].astype(str) + '-' + table['Month'].astype(str) + '-' + '15'
table.drop(['Year', 'Month'], axis=1, inplace=True)





# Inserting client into the database
#list with Dalkia's customer
clients = ['SAIEM', 'EURE HABITAT', 'SECOMILE', 
          'SILOGE' 'COPROPRIETES', 'VILLE EVREUX - EPN', 
          'CONSEIL DEPARTEMENTAL', 'CONSEIL REGIONAL', 
          'GSK', 'DIVERS']
#create a list of list to insert it into the database
list_client = []
for i in clients:
    list_client.append([i])

### BDD insert
mariadb_connection = mariadb.connect(host='mariadb', port=3306, user='dalkia', password = 'esiglec', database='bdd')
cursor = mariadb_connection.cursor()
sql = "INSERT INTO client ( nom ) VALUES ( %s )"
cursor.executemany(sql, list_client)
mariadb_connection.commit()
mariadb_connection.close()

#reading the correspondance between sites and clients
site_client = pd.read_excel(r"./data/puissance_souscrite.xlsx", sheet_name="siteclient")
#insert sites into the database
sql = "INSERT INTO site ( site_client_id, nom ) VALUES ( %s, %s )"
cursor.executemany(sql, site_client[['id_client', 'nom_site']].values.tolist())
mariadb_connection.commit()
mariadb_connection.close()





# Inserting Sites

### BDD Connexion
mariadb_connection = mariadb.connect(host='mariadb', port=3306, user='dalkia', password = 'esiglec', database='bdd')
cursor = mariadb_connection.cursor()

#first we need to extract all site from the database
sql = "SELECT site_id, nom FROM site"
cursor.execute(sql)
sites = cursor.fetchall()
mariadb_connection.close()





#Inserting Consommation
#creating a dataframe with records
df_sites = pd.DataFrame(sites, columns=["id_site", "nom_site"])
#create a dictionnaire for the replace function
dict_site = {}
#insert into the dict : keys = name ; value = id
for i,row in df_sites.iterrows():
    dict_site[row['nom_site']] = row['id_site']

#get problematic names back
name_problem = set()
for i, row in table.iterrows():
    if (type(row['SITES']) == str):
        name_problem.add(row['SITES'])

#fix the issues
#delete the last caracter if it's a ' '
replace_problem = {}
for name in name_problem:
    replacement = list(name)
    i=1
    while (replacement[-i] == " "):
        replacement[-i] = ""
        i = i + 1
    replace_problem[name] = "".join(replacement)

#execute replacement
table['SITES'] = table['SITES'].replace(replace_problem)
table['SITES'] = table['SITES'].replace(dict_site)
### BDD Connexion
mariadb_connection = mariadb.connect(host='mariadb', port=3306, user='dalkia', password = 'esiglec', database='bdd')
cursor = mariadb_connection.cursor()
# insert into the data base
sql = "INSERT INTO consommation (consommation_site_id, kwh, consommation_date_conso) VALUES (%s, %s, %s)"
cursor.executemany(sql, table.values.tolist())
mariadb_connection.commit()
mariadb_connection.close()