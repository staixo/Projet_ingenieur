import requests
import pandas as pd
import numpy as np

import mysql.connector as mariadb

## Weather API
### Call api and insert response into a dataframe
res = requests.get("https://opendata.reseaux-energies.fr/api/records/1.0/search/?dataset=temperature-quotidienne-regionale&rows=1063&facet=date&facet=region&refine.region=Normandie")
j = res.json()
df = pd.DataFrame(columns=["date", "tmax", "tmin", "tmoy"])

for i,record in enumerate(j["records"]):
    df.loc[i] = [record["fields"]["date"], record["fields"]["tmax"], record["fields"]["tmin"], record["fields"]["tmoy"]]


df["date"] = pd.to_datetime(df["date"], format="%Y-%m-%d")
df.set_index("date", inplace= True)
groupby = df.groupby([df.index.year, df.index.month])

table = groupby.aggregate({"tmax": np.max, "tmin":np.min, "tmoy": np.mean})
table.index.names = ["year", "month"]
table = table.reset_index()

def round_of_rating(number):
    """Round a number to the closest half integer.
    >>> round_of_rating(1.3)
    1.5
    >>> round_of_rating(2.6)
    2.5
    >>> round_of_rating(3.0)
    3.0
    >>> round_of_rating(4.1)
    4.0"""

    return round(number * 2) / 2

for column in ["tmax", "tmin", "tmoy"]:
    table[column] = table[column].apply(lambda x: round_of_rating(x))

## World Data Bank
## GET request API World Bank
### Ancienne valeur moyenne des températures en France
data_past = requests.get('http://climatedataapi.worldbank.org/climateweb/rest/v1/country/mavg/tas/1980/1999/fra').json()
df_past = pd.DataFrame.from_dict(data_past)
data_futur = requests.get('http://climatedataapi.worldbank.org/climateweb/rest/v1/country/mavg/tas/2020/2039/fra').json()
df_futur = pd.DataFrame.from_dict(data_futur)

## Fonction servant réorganiser les dataframes
def getTemp(df):
    temp = pd.DataFrame()
    ## Préparation du remplacement des valeurs de la colonne Mois
    value_to_replace = {0: 1, 1: 2, 2: 3, 3: 4, 4: 5, 5: 6, 6: 7, 7: 8, 8: 9, 9: 10, 10: 11, 11: 12}
    for i in range(len(df)):
        temp[str(df.fromYear[0]+i)] = (df.monthVals[i])
    temp = temp.stack()
    ## Modification des noms de colonnes
    temp = pd.DataFrame(temp.reset_index())
    temp.columns = ['month', 'year','tmoy']
    ## Remplacement des numéros par le nom de mois
    temp['month'] = temp['month'].map(value_to_replace)
    return temp   

## Aggregation des deux dataframes
temperature = pd.concat([getTemp(df_past), getTemp(df_futur)])

## Merge
meteo = table.append(temperature)

## Data type
meteo['date'] = meteo['year'].astype(str) + '-' + meteo['month'].astype(str) + '-' + '15'

## Drop useless columns
meteo = meteo.drop('year', 1)
meteo = meteo.drop('month', 1)

## Rearrange columns order
cols = meteo.columns.tolist()
cols = cols[-1:] + cols[:-1]
meteo = meteo[cols]


### BDD insert
mariadb_connection = mariadb.connect(host='mariadb', port=3306, user='dalkia', password = 'esigelec', database='bdd')
cursor = mariadb_connection.cursor()

sql = "INSERT INTO meteo (meteo_date, temp_min, temp_max, temp_moy) VALUES (%s, %s, %s, %s)"

cursor.executemany(sql, meteo.where(meteo.notnull(), None).values.tolist())

mariadb_connection.commit()

mariadb_connection.close()