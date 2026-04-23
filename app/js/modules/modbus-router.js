export function modbusRouterModeChange()
{
    var mode = document.getElementById('mode');
    if (mode.value == '0') {
        $('#page_rtu_to_tcp').show();
    } else {
        $('#page_rtu_to_tcp').hide();
    }
}

globalThis.modbusRouterModeChange = modbusRouterModeChange;

export function enableModbusRouterCom(checkbox, num)
{
    if (checkbox.checked == true) {
        $('#page_modbus_router_com' + num).show();
    } else {
        $('#page_modbus_router_com' + num).hide();
    }
}

globalThis.enableModbusRouterCom = enableModbusRouterCom;

export function enableModbusRouter(state) {
    if (state) {
      $('#page_modbus_router').show();
      modbusRouterModeChange();
    } else {
      $('#page_modbus_router').hide();
    }
}

globalThis.enableModbusRouter = enableModbusRouter;

export function initModbusRouter () {
    ;
}