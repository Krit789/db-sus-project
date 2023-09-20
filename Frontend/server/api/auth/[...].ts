import CredentialsProvider from "next-auth/providers/credentials";
import { NuxtAuthHandler } from "#auth";

export default NuxtAuthHandler({
    secret: process.env.AUTH_SECRET,
    callbacks: {
        // Callback when the JWT is created / updated, see https://next-auth.js.org/configuration/callbacks#jwt-callback
        jwt: async ({ token, user }) => {
            const isSignIn = user ? true : false;
            if (isSignIn) {
                token.id = user ? user.id || "" : "";
                token.role = user ? (user as any).role || "" : "";
                token.firstName = user ? (user as any).name || "" : "";
                token.lastName = user ? (user as any).lastName || "" : "";
                token.tel = user ? (user as any).tel || "" : "";
                token.email = user ? (user as any).email || "" : "";
                token.token = user ? (user as any).token || "" : "";
            }
            return Promise.resolve(token);
        },
        // Callback whenever session is checked, see https://next-auth.js.org/configuration/callbacks#session-callback
        session: async ({ session, token }) => {
            (session as any).role = token.role;
            (session as any).uid = token.id;
            (session as any).firstName = token.firstName;
            (session as any).lastName = token.lastName;
            (session as any).email = token.email;
            (session as any).tel = token.tel;
            // (session as any).token = token.token; // No need to send token back to user every callback
            return Promise.resolve(session);
        },
    },
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
                    "http://localhost:3000/proxy/api/account/login-user.php",
                    {
                        method: "POST",
                        body: {
                            email: credentials.email,
                            password: credentials.password,
                        },
                    },
                ).catch((error) => error);

                if (data?.status == 1) {
                    //   console.log("This Works!");
                    // console.log(data.jwt);
                    const decodedData = parseJwt(data.jwt);
                    // console.log(decodedData);
                    const u = {
                        id: decodedData?.data.id,
                        email: decodedData?.data.email,
                        name: decodedData?.data.fn,
                        lastName: decodedData?.data.ln,
                        tel: decodedData?.data.tele,
                        role: decodedData?.data.role,
                        token: decodedData?.data.token,
                    };
                    // console.log(u);
                    return u;
                } else {
                    console.error(
                        "Warning: Malicious login attempt registered, bad credentials provided",
                    );
                    return null;
                }
            },
        }),
    ],
});

function parseJwt(token: string) {
    const base64Url = token.split(".")[1];
    const base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");
    const buff = new Buffer(base64, "base64");
    const payloadinit = buff.toString("ascii");
    const payload = JSON.parse(payloadinit);
    return payload;
}
