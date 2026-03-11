from fastapi import FastAPI, Depends
from fastapi.security.api_key import APIKey, HTTPException
from pydantic import BaseModel
from typing import Optional
import sqlite3
import auth
import json
import modules.system as system
import modules.ap as ap
import modules.client as client
import modules.dns as dns
import modules.dhcp as dhcp
import modules.ddns as ddns
import modules.firewall as firewall
import modules.networking as networking
import modules.openvpn as openvpn
import modules.wireguard as wireguard


tags_metadata = [
]
app = FastAPI(
    title="API for ElastPro",
    openapi_tags=tags_metadata,
    version="0.0.1",
    license_info={
    "name": "Apache 2.0",
    "url": "https://www.apache.org/licenses/LICENSE-2.0.html",
    }
)

DB_PATH = "database.db"

class Item(BaseModel):
    key: str
    value: Optional[str] = None

def init_db():
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        key TEXT NOT NULL,
        value TEXT
    )
    """)
    conn.commit()
    conn.close()

@app.get("/system", tags=["system"])
async def get_system(api_key: APIKey = Depends(auth.get_api_key)):
    return{
'hostname': system.hostname(),
'model': system.model(),
'uptime': system.uptime(),
'systime': system.systime(),
'usedMemory': system.usedMemory(),
'processorCount': system.processorCount(),
'LoadAvg1Min': system.LoadAvg1Min(),
'systemLoadPercentage': system.systemLoadPercentage(),
'systemTemperature': system.systemTemperature(),
'hostapdStatus': system.hostapdStatus(),
'operatingSystem': system.operatingSystem(),
'kernelVersion': system.kernelVersion(),
'rpiRevision': system.rpiRevision()
}

@app.get("/ap", tags=["WiFi AP"])
async def get_ap(api_key: APIKey = Depends(auth.get_api_key)):
    return{
'driver': ap.driver(),
'ctrl_interface': ap.ctrl_interface(),
'ctrl_interface_group': ap.ctrl_interface_group(),
'auth_algs': ap.auth_algs(),
'wpa_key_mgmt': ap.wpa_key_mgmt(),
'beacon_int': ap.beacon_int(),
'ssid': ap.ssid(),
'channel': ap.channel(),
'hw_mode': ap.hw_mode(),
'ieee80211n': ap.ieee80211n(),
'wpa_passphrase': ap.wpa_passphrase(),
'interface': ap.interface(),
'wpa': ap.wpa(),
'wpa_pairwise': ap.wpa_pairwise(),
'country_code': ap.country_code(),
'ignore_broadcast_ssid': ap.ignore_broadcast_ssid()
}

@app.get("/clients/{wireless_interface}", tags=["Clients"]) 
async def get_clients(wireless_interface, api_key: APIKey = Depends(auth.get_api_key)):
    return{
'active_clients_amount': client.get_active_clients_amount(wireless_interface),
'active_clients': json.loads(client.get_active_clients(wireless_interface))
}

@app.get("/dhcp", tags=["DHCP"])
async def get_dhcp(api_key: APIKey = Depends(auth.get_api_key)):
    return{
'range_start': dhcp.range_start(),
'range_end': dhcp.range_end(),
'range_subnet_mask': dhcp.range_subnet_mask(),
'range_lease_time': dhcp.range_lease_time(),
'range_gateway': dhcp.range_gateway(),
'range_nameservers': dhcp.range_nameservers()
}

@app.get("/networking", tags=["Networking"]) 
async def get_networking(api_key: APIKey = Depends(auth.get_api_key)):
    return{
'interfaces': json.loads(networking.interfaces()),
'throughput': json.loads(networking.throughput())
}

init_db()

@app.post("/items", tags=["Custom"])
def create_item(item: Item, api_key: APIKey = Depends(auth.get_api_key)):
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    cursor.execute("INSERT INTO items (key, value) VALUES (?, ?)", 
                   (item.key, item.value))
    conn.commit()
    conn.close()
    return {"message": "Item created successfully"}

@app.put("/items/{item_id}", tags=["Custom"])
def update_item(item_id: int, item: Item, api_key: APIKey = Depends(auth.get_api_key)):
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    cursor.execute("UPDATE items SET key=?, value=? WHERE id=?", 
                   (item.key, item.value, item_id))
    conn.commit()
    if cursor.rowcount == 0:
        conn.close()
        raise HTTPException(status_code=404, detail="Item not found")
    conn.close()
    return {"message": "Item updated successfully"}

@app.delete("/items/{item_id}", tags=["Custom"])
def delete_item(item_id: int, api_key: APIKey = Depends(auth.get_api_key)):
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    cursor.execute("DELETE FROM items WHERE id=?", (item_id,))
    conn.commit()
    if cursor.rowcount == 0:
        conn.close()
        raise HTTPException(status_code=404, detail="Item not found")
    conn.close()
    return {"message": "Item deleted successfully"}

@app.get("/items", tags=["Custom"])
def list_items(api_key: APIKey = Depends(auth.get_api_key)):
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    cursor.execute("SELECT id, key, value FROM items")
    rows = cursor.fetchall()
    conn.close()
    return [{"id": r[0], "key": r[1], "value": r[2]} for r in rows]

