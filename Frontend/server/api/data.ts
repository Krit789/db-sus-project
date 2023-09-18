import {getServerSession} from "#auth";

export default defineEventHandler(async (event) => {
    const session = await getServerSession(event); // Get user session
    const body = await readBody(event); // Read from POST body
    if (session?.token) {
        const reservations = await $fetch(
            "http://localhost:3000/proxy/api/control.php",
            {
                method: "POST",
                body: {
                    type: body.type,
                    token: session?.token,
                },
            },
        ).catch((error) => error.data);
        return reservations;
    }
    setResponseStatus(event, 418);
    return {
        status: 0,
        message: "Invalid Session Token",
    };
});
