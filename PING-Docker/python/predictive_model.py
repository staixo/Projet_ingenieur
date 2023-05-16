# Predictive model

## Pakages import
### database connection packages
import requests
import mysql.connector as mariadb
## Dataframe packages
import pandas as pd
import numpy as np

## Data import
### Database connection
mariadb_connection = mariadb.connect(user='dalkia', password='esigelec', host="localhost", port="3307", database='bdd')
cursor = mariadb_connection.cursor()
### SQL queries
sql_conso = "SELECT consommation_id, consommation_site_id, consommation_date_conso, kwh FROM consommation"
sql_dju = "SELECT * FROM degresjour_dju"
sql_para = "SELECT * FROM parametre"
### Read Data
cursor.execute(sql_conso)
conso = pd.DataFrame(cursor.fetchall())
cursor.execute(sql_dju)
dju = pd.DataFrame(cursor.fetchall())
cursor.execute(sql_para)
para = pd.DataFrame(cursor.fetchall())
### Connection close
mariadb_connection.close()

## Dataframe configuration
### Rename columns
conso.columns = ["consommation_id", "consommation_site_id", "date", "kwh"]
dju.columns = ["dju_id", "date", "dju_valeur"]
para.columns = ["parametre_id", "scenario_meteo", "puissance_souscrite", "pourcentage_reduction_isolation"]
### Add year column into dju to only get the year value instead of datetime
dju['year'] = pd.DatetimeIndex(dju['date']).year

### Cold year choice
#### We sum dju_valeur by year to find the maximum dju_valeur
dju["sum"] = dju.groupby(["year"])["dju_valeur"].transform(sum)
cold_year = dju.loc[dju['sum'].idxmax()]
cold_year = cold_year.year
### Dataframe that contains date and dju_valeur for the coldest year
cold_dju = pd.DataFrame(dju[["date","dju_valeur"]][dju.year == cold_year])

### Warm year choice
warm_year = dju.loc[dju['sum'].idxmin()]
warm_year = warm_year.year
### Dataframe that contains date and dju_valeur for the warmest year
warm_dju = pd.DataFrame(dju[["date","dju_valeur"]][dju.year == warm_year])

### We select the last year from the dataset
stable_year = conso.loc[conso['date'].dt.year.idxmax()]
stable_year = stable_year.date.year

### We merge conso and dju and drop consommation_id columns
conso_dju = pd.merge(conso, dju[["dju_valeur", "date"]], on='date')
conso_dju = conso_dju.drop(columns=["consommation_id"])

## Model

### Stable scenario : we found the mean for each client data by month
stable_model = conso_dju.groupby(['consommation_site_id',conso_dju.date.dt.month],as_index=False).mean()
temp_date = pd.DataFrame(columns=["date"])
for i in range(0,len(stable_model),12):
    for j in range(1,13):
        temp_date = temp_date.append({'date': pd.Timestamp(year=stable_year+1, month=j, day=15)},ignore_index=True)

stable_model['date'] = temp_date

### We create every predictions for each parameters
para_stable = pd.DataFrame(para[["parametre_id","pourcentage_reduction_isolation"]][para.scenario_meteo == "Stable"])
stable_predict = pd.DataFrame(columns=['parametre_id','consommation_site_id','date', 'kwh'])
for l in range(0, len(stable_model), 12): 
    for i in range(0, len(para_stable)):
        for j in range(1, 13):
            stable_predict = stable_predict.append({
                'parametre_id': para_stable.loc[i,["parametre_id"]].parametre_id,
                'date': pd.Timestamp(year=stable_year+1, month=j, day=15),
                'consommation_site_id': stable_model.loc[l,["consommation_site_id"]].consommation_site_id,
                'kwh': stable_model.loc[l+j-1,["kwh"]].kwh - (stable_model.loc[l+j-1,["kwh"]].kwh*para_stable.loc[i,["pourcentage_reduction_isolation"]].pourcentage_reduction_isolation)},
                 ignore_index=True)

### Cold year scenario : we use the coldest year from our data
cold_pred = pd.DataFrame(columns=['consommation_site_id','date', 'kwh', 'dju','kwh_pred'])
temp2 = pd.DataFrame(columns=["kwh"])
for j in range(1, conso_dju['consommation_site_id'].max()+1): 
    df = conso_dju.loc[conso_dju['consommation_site_id']==j].sort_values(by='date', ascending=True)
    temp2 = temp2.append(pd.DataFrame(df[['kwh','dju_valeur']].loc[df['date'].dt.year == stable_year]),ignore_index=True)
    for i in range(1,13):
        cold_pred = cold_pred.append({'date': pd.Timestamp(year=stable_year+1, month=i, day=15),
                                        'consommation_site_id': j},ignore_index=True)
cold_pred[['dju','kwh']] = temp2

### We create every predictions for each parameters
for l in range(0, len(cold_pred), 12): 
    for m in range(0, len(cold_dju)):
        cold_pred.loc[cold_pred.index[l+m], 'kwh_pred'] = cold_dju.iloc[m].dju_valeur*cold_pred.iloc[l+m].kwh/cold_pred.iloc[l+m].dju

cold_pred = cold_pred.drop(columns=['kwh','dju'])
cold_pred.columns = ['consommation_site_id','date', 'kwh']
para_cold = pd.DataFrame(para[["parametre_id","pourcentage_reduction_isolation"]][para.scenario_meteo == "Froide"])
cold_predict = pd.DataFrame(columns=['parametre_id','consommation_site_id','date', 'kwh'])
for l in range(0, len(cold_pred), 12): 
    for i in range(0, len(para_cold)):
        for j in range(1, 13):
            cold_predict = cold_predict.append({
                'parametre_id': para_cold.iloc[i].parametre_id,
                'date': pd.Timestamp(year=stable_year+1, month=j, day=15),
                'consommation_site_id': cold_pred.loc[l,["consommation_site_id"]].consommation_site_id,
                'kwh': cold_pred.loc[l+j-1,["kwh"]].kwh - (cold_pred.loc[l+j-1,["kwh"]].kwh*para_cold.iloc[i].pourcentage_reduction_isolation)},
                 ignore_index=True)


### Warm year scenario : we use the warmest year from our data
warm_pred = pd.DataFrame(columns=['consommation_site_id','date', 'kwh', 'dju','kwh_pred'])
temp3 = pd.DataFrame(columns=["kwh"])
for j in range(1, conso_dju['consommation_site_id'].max()+1): 
    df = conso_dju.loc[conso_dju['consommation_site_id']==j].sort_values(by='date', ascending=True)
    temp3 = temp3.append(pd.DataFrame(df[['kwh','dju_valeur']].loc[df['date'].dt.year == stable_year]),ignore_index=True)
    for i in range(1,13):
        warm_pred = warm_pred.append({'date': pd.Timestamp(year=stable_year+1, month=i, day=15),
                                        'consommation_site_id': j},ignore_index=True)
warm_pred[['dju','kwh']] = temp3

#### We create every predictions for each parameters
for l in range(0, len(warm_pred), 12): 
    for m in range(0, len(warm_dju)):
        warm_pred.loc[warm_pred.index[l+m], 'kwh_pred'] = warm_dju.iloc[m].dju_valeur*warm_pred.iloc[l+m].kwh/warm_pred.iloc[l+m].dju

warm_pred = warm_pred.drop(columns=['kwh','dju'])
warm_pred.columns = ['consommation_site_id','date', 'kwh']
para_warm = pd.DataFrame(para[["parametre_id","pourcentage_reduction_isolation"]][para.scenario_meteo == "Chaude"])
warm_predict = pd.DataFrame(columns=['parametre_id','consommation_site_id','date', 'kwh'])
for l in range(0, len(warm_pred), 12): 
    for i in range(0, len(para_warm)):
        for j in range(1, 13):
            warm_predict = warm_predict.append({
                'parametre_id': para_warm.iloc[i].parametre_id,
                'date': pd.Timestamp(year=stable_year+1, month=j, day=15),
                'consommation_site_id': warm_pred.loc[l,["consommation_site_id"]].consommation_site_id,
                'kwh': warm_pred.loc[l+j-1,["kwh"]].kwh - (warm_pred.loc[l+j-1,["kwh"]].kwh*para_warm.iloc[i].pourcentage_reduction_isolation)},
                 ignore_index=True)

## Result : we concatenate prediction together and rearrange the dataframe prediction
prediction = pd.concat([stable_predict, cold_predict, warm_predict])
prediction["parametre_id"] = prediction["parametre_id"].astype(int)
prediction.index += 1 
prediction['prediction_id'] = prediction.index
prediction['date'] = prediction['date'].apply(lambda x: x.strftime('%Y-%m-%d'))
cols = prediction.columns.tolist()
cols = cols[-1:] + cols[:-1]
prediction = prediction[cols]
prediction = prediction.drop(columns=["prediction_id"])

## Insert into prediction table
### Database connection
mariadb_connection = mariadb.connect(user='dalkia', password='esigelec', host="localhost", port="3307", database='bdd')
cursor = mariadb_connection.cursor()
### SQL query
sql = "INSERT INTO prediction (parametre_id,prediction_site_id,prediction_date,prediction_value) VALUES (%s, %s, %s, %s)"
cursor.executemany(sql, prediction.values.tolist())

## Finnaly we commit and close the connection
mariadb_connection.commit()
mariadb_connection.close()