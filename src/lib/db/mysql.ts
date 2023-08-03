import mysql from "mysql2/promise";

let mysqlconn: mysql.Connection;

export function mysqlconnFn() {
  if (!mysqlconn) {
    mysqlconn = mysql.createPool({
      host: "49.228.131.109",
      port: 3357,
      user: "test_user",
      password: "Pu57HDrrXgvVLn",
      database: "susproject",
    });
  }
  mysqlconn.on('connection', function (connection) {
    console.log('DB Connection established');
  
    connection.on('error', function (err) {
      console.error(new Date(), 'MySQL error', err.code);
    });
    connection.on('close', function (err) {
      console.error(new Date(), 'MySQL close', err);
    });
  });
  
  return mysqlconn;
}