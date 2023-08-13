import CredentialsProvider from "next-auth/providers/credentials";
import { NuxtAuthHandler } from "#auth";

export default NuxtAuthHandler({
  secret: "asdasdasd", // process.env.AUTH_SECRET,
  providers: [
    CredentialsProvider.default({
      name: "Credentials",
      credentials: {
        email: {
          label: "E-Mail",
          type: "text",
          placeholder: "(hint: jsmith)",
        },
        password: {
          label: "Password",
          type: "password",
          placeholder: "(hint: hunter2)",
        },
      },
      async authorize(credentials: any) {
        const data: any = await $fetch(
          "http://localhost:3000/proxy/api/auth-file/login-user.php",
          {
            method: "POST",
            body: {
              email: credentials.email,
              password: credentials.password,
            },
          }
        ).catch((error) => error);
        
        if (data?.status == 1) {
          //   console.log("This Works!");
            // console.log(data)
          return data
        } else {
          console.error(
            "Warning: Malicious login attempt registered, bad credentials provided"
          );
          return null;
        }
      },
    }),
  ],
});
