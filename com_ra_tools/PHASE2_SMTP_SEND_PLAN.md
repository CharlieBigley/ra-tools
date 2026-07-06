# Phase 2 SMTP Send API

## Purpose

This note records the current findings and the recommended implementation plan for the optional Phase 2 work to send email through a third-party SMTP provider API.

Phase 2 is explicitly lower priority than Phase 1. The current Joomla mail transport remains acceptable in production. This document exists so a later agent session can start the send work with the design context already captured.

## Current Position

- `com_ra_ramblers` is in production and sensitive to change.
- `com_ra_members` is still in development and is the safer place to prove new integration patterns.
- Per-message failure interrogation is the primary requirement and must be delivered first.
- Provider-based sending is a nice-to-have enhancement, not a prerequisite for the new project.
- The Joomla mail stack in `ToolsHelper::sendEmail()` currently works and should not be replaced speculatively.

## Existing Code Anchors

- `com_ra_tools/site/src/Helpers/ToolsHelper.php`
  - Owns the current send path via `sendEmail()`.
  - Already includes email logging behaviour and should remain the stable public entry point for callers.
- `com_ra_members/site/src/Helper/LoadHelper.php`
  - Shows the working pattern for loading a site record from `#__ra_api_sites` and making authenticated remote calls.
- `com_ra_tools/site/src/Helpers/JsonHelper.php`
  - Experimental helper.
  - Useful as a source of ideas, but not yet a clean abstraction layer.
  - Currently mixes token lookup, auth-header selection, transport, and response parsing.
- `#__ra_api_sites`
  - Already exists in `com_ra_tools` and is the current source of tokens and remote base URLs.
- `#__ra_emails`
  - Already exists in `com_ra_tools` and can continue to serve as local send logging.

## Main Recommendation

Do not implement Phase 2 by directly rewriting the production `ToolsHelper::sendEmail()` logic.

Instead:

1. Keep `ToolsHelper::sendEmail()` as the public send entry point.
2. Add a configuration switch in `com_ra_tools` to choose transport.
3. When disabled, continue to use the Joomla mailer exactly as now.
4. When enabled, delegate to a provider-specific send service.
5. Place provider-specific logic behind a narrow interface so that a later provider can be swapped in with minimal change.

This keeps risk low for production code and avoids forcing all current callers to change.

## Recommended Ownership Model

The cleanest long-term model is a standalone delivery component that provides a simple local service interface for:

- sending mail through a provider API
- polling provider delivery events
- normalising provider responses
- storing provider event data locally

Rationale:

- `com_ra_tools` already contains shared infrastructure such as `#__ra_api_sites` and `#__ra_emails`.
- `com_ra_members` is a good proving ground, but delivery-provider integration is broader than membership logic.
- A standalone delivery component would allow a different provider to be introduced later as a drop-in implementation.

For Phase 2 specifically, that component should expose a sending service which can be called from `ToolsHelper`.

## Why Not Use `/api`

The Joomla `/api` application is not the correct destination for this work.

In this context, `/api` is a self-contained mechanism for remote calls from one Joomla website to another. The SMTP provider integration is different:

- it is an internal service concern
- it is used locally by components and batch jobs
- it does not need to be exposed as a public remote API surface

Therefore the provider adapter should live in a helper/service layer, not as a Joomla `/api` application endpoint.

## Auth Findings

There are several distinct auth patterns in the current code and requirements:

1. Joomla-style token headers for inter-site Joomla calls
2. Bearer-token headers for some current remote integrations
3. Provider-specific API key headers
4. Provider-specific API keys in the JSON payload

The existing `JsonHelper` blurs these patterns and should not be treated as the final transport abstraction.

For SMTP2GO specifically, the provider docs indicate the use of `X-Smtp2go-Api-Key` or an equivalent `api_key` payload field, not bearer auth.

Implication for Phase 2:

- The transport layer must support multiple auth strategies.
- The send implementation must not assume bearer auth simply because some current Joomla-to-Joomla integrations use it.

## Phase 2 Scope

Phase 2 should cover only the optional provider-based send path.

It should not include:

- replacing Joomla mail transport by default
- exposing public API endpoints
- rewriting existing callers
- changing production behaviour unless explicitly enabled by configuration

## Send Path Design

### Stable Caller Contract

Callers should continue to use `ToolsHelper::sendEmail()`.

That method should become a transport selector:

- `joomla` transport: current code path
- `provider_api` transport: delegated code path

This is the minimum-risk integration point.

### Suggested Internal Interface

The provider adapter should present a narrow service contract, for example:

- `send(array $message): SendResult`

Where the normalised message object contains:

- sender
- to
- cc
- bcc
- reply_to
- subject
- html_body
- text_body
- attachments
- metadata

And the result object contains:

- success flag
- provider name
- provider message id if available
- local message id if available
- raw response payload
- error message and error code if failed

## Data and Correlation Requirements

Per-message tracking is the main reason to be careful with Phase 2.

If provider sending is ever introduced, each outbound message should be given a local correlation identity which can later be matched to provider events.

Recommended approach:

1. Create or reuse a local outbound message record before sending.
2. Generate a stable local message key.
3. Pass that key to the provider if the provider allows message metadata or custom headers.
4. Store the provider response, including provider message id.
5. Use that information later to reconcile delivery events.

Without this, Phase 1 event interrogation can only link records heuristically.

## Attachment Handling

The current Joomla mailer can accept filesystem attachments directly. A provider API usually requires attachment objects, typically with:

- filename
- content type
- base64-encoded content

Therefore the provider adapter must be responsible for converting the current attachment input format into the provider payload.

This conversion logic should not leak into existing callers.

## Logging and Audit

Phase 2 should preserve the existing operational behaviour as far as possible:

- keep existing send logging to `#__ra_emails`
- add provider-specific response logging where useful
- capture failures in enough detail for diagnosis without forcing callers to inspect raw provider responses

At minimum, log:

- transport selected
- local message id
- provider message id if available
- success or failure
- provider error text

## Configuration Changes Needed

If Phase 2 proceeds, `com_ra_tools` should gain settings such as:

1. Enable provider send transport
2. Selected provider
3. Provider API site reference
4. HTTP timeout
5. Fast-accept mode if supported by the provider
6. Optional dry-run or log-only mode

These settings should be additive. The Joomla transport must remain available.

## Suggested Implementation Sequence

1. Define the provider transport interface.
2. Define the normalised send request and send result structures.
3. Add the `com_ra_tools` configuration switch for transport selection.
4. Refactor `ToolsHelper::sendEmail()` only enough to delegate to the selected transport.
5. Implement the SMTP2GO adapter as the first provider.
6. Add attachment conversion and provider response parsing.
7. Persist local and provider correlation identifiers.
8. Add narrow validation and live testing behind the config flag.
9. Only then consider enabling the provider path in non-development environments.

## Risks

Main risks for Phase 2 are:

- changing production send behaviour accidentally
- weak message correlation between local sends and provider events
- attachment conversion mismatches
- provider-specific auth assumptions leaking into generic code
- making `JsonHelper` carry too many responsibilities without first separating its concerns

## Recommended Refactor Direction for `JsonHelper`

`JsonHelper` may still be useful, but only after responsibilities are separated. If refactored later, it should move towards:

1. API site lookup
2. auth strategy selection
3. HTTP transport execution
4. response parsing
5. error normalisation

The current helper should be treated as experimental and not as the final Phase 2 transport surface.

## Open Questions Before Phase 2 Starts

1. Which provider fields are available for per-message metadata or custom headers?
2. Can the provider return a stable provider message id at send time?
3. What is the exact attachment payload format and size limit?
4. Does the provider expose synchronous acceptance only, or final send status in the send response?
5. Should the standalone delivery component own both outbound message logging and event storage, or should `#__ra_emails` remain the send log of record?

## Recommended Phase 2 Outcome

The desired end state for Phase 2 is:

- callers still call `ToolsHelper::sendEmail()`
- transport is chosen by configuration
- Joomla transport remains available and is the safe fallback
- provider sending is implemented behind a replaceable adapter
- local and provider message identities are recorded for later delivery reconciliation
- the design remains compatible with later support for a different SMTP provider
