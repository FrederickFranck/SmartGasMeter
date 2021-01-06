from firebase import firebase
import datetime
import uuid
import toml



CONFIG = toml.load('config.toml')
userId = CONFIG.get("User").get("ID")
name = CONFIG.get("GPIO").get("Name")

def main():
    if(userId is None):
        create_new_user(name)
        update_toml()



gasMeterValue = 69
firebase = firebase.FirebaseApplication("https://smartgasmeterv2-106f6-default-rtdb.firebaseio.com/",None)

def update_db():
    database_path = "/Users/User{}".format(userId)
    result = firebase.put(database_path,"GasMeterValue","{}%".format(gasMeterValue))
    result = firebase.put(database_path,"LastUpdate",(datetime.datetime.now().strftime("\"%d/%m %H:%M\"")))

def create_new_user(name):
    userId = str(uuid.uuid4())

    database_path = "/Users/User{}".format(userId)
    result = firebase.put(database_path,"GasMeterValue","{}%".format(gasMeterValue))
    result = firebase.put(database_path,"LastUpdate",(datetime.datetime.now().strftime("\"%d/%m %H:%M\"")))
    result = firebase.put("/Users/User456","Name",name)


def update_toml():
    toml_dict = {
        "Title": "SmartGasMeter Config",
        "User":{
                "ID": userId,
                "Name": name
        }
    }
    with open(config.toml) as file:
        toml.dump(toml_dict, file)

main()
