import mysql from "mysql2/promise";

let mysqlconn = null;

export function mysqlconnFn() {
  if (!mysqlconn) {
    mysqlconn = mysql.createConnection({
      host: "49.228.131.109",
      port: 3357,
      user: "test_user",
      password: "Pu57HDrrXgvVLn",
      database: "susproject",
    });
  }

  return mysqlconn;
}