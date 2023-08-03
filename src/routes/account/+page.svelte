<script>
	import { AppBar } from '@skeletonlabs/skeleton';
	import { json } from '@sveltejs/kit';
	// export let data;
	import Account from 'svelte-material-icons/account.svelte';
	let result;
    let userID;
	async function loadUser(userID) {
		const res = await fetch('http://localhost:5173/api/user', {
			method: 'POST',
			body: JSON.stringify({
				user: userID
			}),
			headers: {
				'Content-Type': 'application/json'
			}
		});
		const json = await res.json();
		result = json;
	}
</script>

<AppBar>
	<svelte:fragment slot="lead"><Account color="black" width="32" height="32" /></svelte:fragment>
	<svelte:fragment slot="trail"><a>Hello</a><a>Hello2</a><a>Hello3</a></svelte:fragment>
	<svelte:fragment slot="headline"
		><h1 class="text-4xl font-black"><a href="/">SUS App</a></h1></svelte:fragment
	>
</AppBar>

<!-- {#each data.data as d}
    <p>{d.first_name}</p>
{/each} -->

<!-- {JSON.stringify(result)} -->

{JSON.stringify(result)}
<label class="label">
	<span>Input</span>
	<input class="input" type="text" placeholder="Input" bind:value={userID}/>
</label>
<button type="button" class="btn variant-filled" on:click={() => loadUser(userID)}>Fetch Data</button>
<!-- {#each data.user as d}
    <p>{JSON.stringify(d)}</p>
{/each} -->
