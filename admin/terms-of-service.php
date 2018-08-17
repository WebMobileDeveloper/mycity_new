<?php
include("header.php");
include_once 'includes/db.php';
include_once 'includes/functions.php';
if (isset($_SESSION['user_id'])) {
    header('location: dashboard.php');
}
$groups = getGroups($link);
$vocations = getVocations($link);
?>
    <section id="main-section" class="privacy-policy"> 
		<div class='text-center'>
		<div class="container">
            <div class="row">
			<div class="col-md-10 col-md-offset-1">
				<img src='images/edge-logo.gif' alt='MyCity' width='240'/>
				
				 <h1 class="text-center">Mycity.com Terms of Service</h1>

			</div>
			</div>
			</div>
		</div>
	
        <div class="container marg6">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                   
                    <p class="description"> 
Welcome to the Terms of Service Agreement for Mycity.com This agreement describes the terms on which you may access and use our services. To become an Mycity.com user, accept all of the terms and conditions of this agreement as well as our Privacy Policy. In the event of any inconsistency between the Mycity.com Privacy Policy and this User Agreement, the User Agreement shall prevail. If you do not agree to be bound by the terms of this Agreement, you may not use nor access our services.
					</p>
<p>We reserve the right to modify this Agreement at any time, and without prior notice, by posting amended terms on this agreement. Your continued use of the Mycity.com indicates your acceptance of the amended User Agreement.
</p>
  
  
<h2>Description of Service</h2> 
<p>Mycity.com is a lead generation tool that uses a matching algorithm to bring professionals together to do business. Mycity.com is typically used by marketers, sales professionals, small business owners, recruiters, job candidates and more. Our service generates leads based on common needs and location between two individuals.</p>
 
<h2>Membership Eligibility</h2>
<p>
The Mycity.com service is not available to minors under the age of 18 or to any users suspended or removed from the system by Mycity.com for any reason. Additionally, users are prohibited from selling, trading, or otherwise transferring their Mycity.com account to another party. If you do not qualify, you may not use the Mycity.com service.
</p>

<h2>Use of Service</h2>

<p>
Your use of Mycity.com is governed by this User Agreement. Mycity.com may refuse service without prior notice to any user for any or no reason.
</p>
<p>
You are responsible for maintaining the confidentiality of your password and account, and are fully responsible for all activities that occur under your password or account with or without your knowledge. If you knowingly provide your login and password information to another person, your account privileges may be suspended temporarily or terminated. You agree to immediately notify Mycity.com of any unauthorized use of your password or account or any other breach of security Mycity.com cannot and will not be liable for any loss or damage arising from your failure to comply with this section.
</p>
 
 
<h2>Termination</h2>
<p>
You agree that Mycity.com may, with or without cause, immediately terminate your account and access to Mycity.com services without prior notice. Without limiting the foregoing, the following will lead to a termination by Mycity.com of a user's account (a) breaches or violations of this User Agreement or other incorporated agreements or guidelines, (b) requests by law enforcement or other government agencies, (c) a request by you (self-initiated account deletions), (d) unexpected technical issues or problems, and (e) extended periods of inactivity. Termination of your Mycity.com account includes removal of access to all offerings within the Mycity.com Service and may also bar you from further use of Mycity.com. Furthermore, you agree that all terminations shall be made in Mycity.com's sole discretion and that Mycity.com shall not be liable to you nor any third-party for any termination of your account or access to the Mycity.com Service.
</p>

 
<h2>User Conduct</h2>
<p>
You understand and agree not to use Mycity.com to:
</p>

Post content or initiate communications which are unlawful, libelous, abusive, obscene, discriminatory, or otherwise objectionable.
Use the Mycity.com Service for any illegal purpose, including but not limited to conspiring to violate laws.
Falsely state, impersonate, or otherwise misrepresent your identity, including but not limited to the use of a pseudonym, or misrepresenting your current or previous positions and qualifications, or your affiliations with a person or entity, past or present.
Upload, post, email, transmit or otherwise make available any content or initiate communications which include information that you do not have the right to disclose or make available under any law or under contractual or fiduciary relationships (such as insider information, or proprietary and confidential information learned or disclosed as part of employment relationships or under nondisclosure agreements).
Upload, post, email, transmit or otherwise make available any content or initiate communication that infringes upon patents, trademarks, trade secrets, copyrights or other proprietary rights.
Upload, post, email, transmit or otherwise make available any unsolicited or unauthorized advertising, promotional materials, “junk mail,” “spam,” “chain letters,” “pyramid schemes,” or any other form of solicitation. This prohibition includes but is not limited to a) Using Mycity.com invitations to send messages to people who are unlikely to recognize you as a known contact; b) Using Mycity.com to connect to people who don't know you and then sending unsolicited promotional messages to those direct connections without their permission; and c) Sending messages to distribution lists, newsgroup aliases, or group aliases.
Upload, post, email, transmit or otherwise make available any material that contains software viruses or any other computer code, files or programs designed to interrupt, destroy or limit the functionality of any computer software or hardware or telecommunications equipment.
Stalk or harass anyone.
Forge headers or otherwise manipulate identifiers in order to disguise the origin of any communication transmitted through the Mycity.com Service.
Post content in fields that aren't intended for that content. Example: Putting an address in a name or title field.
Interfere with or disrupt the Mycity.com Service or servers or networks connected to the Mycity.com Service, or disobey any requirements, procedures, policies or regulations of networks connected to the Mycity.com Service.






<h2>International Use</h2>
<p>
Recognizing the global nature of the Internet, you agree to comply with all applicable local rules including but not limited to rules regarding online conduct and acceptable content. Specifically, you agree to comply with all applicable laws regarding the transmission of technical data exported from the United States or the country in which you reside.
</p>
<h2>Information Provided on this Website</h2>
<p>
In the course of using Mycity.com, users may provide information about themselves which may be visible to certain other users (see our Privacy Policy to learn more about information collected on this website). You understand that by posting materials on the Mycity.com website or otherwise providing materials to Mycity.com, you are granting to Mycity.com a royalty-free, perpetual, irrevocable license to use this information in the course of offering the Mycity.com service. Furthermore, you understand that Mycity.com retains the right to reformat, excerpt, or translate any materials submitted by you. You understand that all information publicly posted or privately transmitted through Mycity.com is the sole responsibility of the person from which such content originated and that Mycity.com will not be liable for any errors or omissions in any content. You understand that Mycity.com cannot guarantee the identity of any other users with whom you may interact in the course of using the Mycity.com Service. Additionally, we cannot guarantee the authenticity of any data which users may provide about themselves or relationships they may describe.
</p>
<p>
In many ways, Mycity.com also serves as a search engine that extracts and summarizes information from other sources across the Internet. This information may be inaccurate and we may make mistakes when extracting the information. Mycity.com assumes no responsibility regarding the accuracy of the information that is provided by Mycity.com. Once we have collected information about a person / company, we combine multiple mentions of the same person into an individual Profile. The resulting Web Summaries are then made available to the public through Mycity.com.
</p>
<p>
Additionally, Mycity.com allows users to create and/or update their own Profile. Any information provided by a user to be part of his or her Profile will be available to the public as part of the Mycity.com service. Users who create and/or update their own Profile can change or delete any information they provide if they choose to do so. Please use caution when posting personal identifiable information.
</p>


<h2>Access to Service</h2>
<p>
Use of manual or automated software, devices, or other processes to “crawl” or “spider” any web pages contained in the Mycity.com website is strictly prohibited. You agree not to monitor or copy, or allow others to monitor or copy, our web pages or the content included herein. You also agree not to “frame” or otherwise simulate the appearance or function of this website. Furthermore, you agree not to take any action that interferes with the proper working of or places an unreasonable load on our infrastructure, including but not limited to unsolicited communications, attempts to gain unauthorized access, or transmission or activation of computer viruses.
</p>


<h2>Communications</h2>
<p>
In the course of providing you services, Mycity.com may need to communicate with you via email (see our Privacy Policy to learn more about communications). You agree to receive emails which are specific to your account and necessary for the normal functioning of Mycity.com, including a series of emails which help inform new users about various features and upgrade opportunities. You also agree to have your name and/or email address listed in the header of certain communications which you initiate through Mycity.com.
</p>

<h2>Uploaded Emails - Third Party Platforms</h2>
<p>
Growing your network often begins with inviting your existing contacts to join you on a new network like Mycity.com. Mycity.com simplifies the process by providing an automated way to invite multiple people across a variety of communication platforms (other social networks, email accounts, etc.). Note: Mycity.com is not liable for any communication made between our users and members of other networks or the networks themselves. Invitations originating from Mycity.com to people outside of the network at the request of our current users are done entirely independent of Mycity.com and therefore absolve Mycity.com from damages or complaints that may be brought by a recipient or third-party platform.
</p>


<h2>Monitoring and Enforcement</h2>
<p>
While we have the right to monitor activity and content associated with Mycity.com, we are not obligated to do so. And since we do not, and may not have the ability to, control or actively monitor content we don't guarantee its accuracy, integrity or quality. Because community standards vary and individuals sometimes choose not to comply with our policies and practices, in the process of using our website, you may be exposed to content that you find offensive or objectionable. You can contact our Customer Service Department to let us know of content that you find objectionable. We may investigate the complaints and violations of our policies that come to our attention and may take any action that we believe is appropriate, including, but not limited to issuing warnings, removing the content or terminating accounts and/or subscriptions. However, because situations and interpretations vary, we also reserve the right not to take any action. Under no circumstances will we be liable in any way for any content, including, but not limited to, any errors or omissions in any content, or any loss or damage of any kind incurred as a result of the use of, access to, or denial of access to any content on the website.
</p>

<h2>Fees</h2>
<p>
Signing up for a basic Mycity.com account is free. Mycity.com also offers a variety of ‘PRO' services that may include a monthly fee and or a one-time ‘upgrade' option or ‘event' charges. All of these charges are presented clearly to our users during each ‘sign-up' process. Subscriptions to Mycity.com are billed on either a monthly (every four weeks), annual basis from the date of the initial sign-up. Mycity.com reserves the right to modify the pricing of, add to, or discontinue an Mycity.com service or any portion thereof without prior notice.
</p>
<p>
Paid members are responsible for keeping their billing information current including expiration dates, billing address, etc. Members whose billing experiences a failure will be notified via email and upon login to their profile. All premium services provided through the membership will cease until updated billing information is provided. Members will have an Mycity.com to update their billing information to re-activate their membership.
</p>


<h2>Right of Cancellation</h2>
<p>
The User may cancel registration for the PRO Membership by going to their account page or emailing Mycity.com at support@myMycity.com.com without stating any particular reason. All PRO Members may cancel at anytime. WHEN YOU CANCEL YOUR ACCOUNT, YOUR ACCOUNT WILL REMAIN ACTIVE UNTIL THE END OF YOUR CURRENT SUBSCRIPTION TERM UNLESS OTHERWISE TERMINATED. SUBSCRIPTION FEES WILL NOT BE PRORATED OR REFUNDED FOR PARTIAL USAGE.
</p>

<h2>Consequences of cancellation</h2>
<p>
In the event of cancellation, both parties shall be obliged to restore any benefits already received in accordance with legal provisions, and issue any gains (e.g. interest). This means that all activity related to your profile becomes the sole possession of Mycity.com including all efforts related to SEO, SEM, etc. So for example, if our SEO efforts get your profile ranked #1 on Google and you decide to cancel, the ranking by virtue of the profile (owned by Mycity.com) becomes the possession of Mycity.com. Mycity.com has the right to use the profile ranking thereafter however it wishes.
</p>

<h3>Indemnification</h3>
<p>
By accepting this User Agreement, you agree to indemnify and otherwise hold harmless Mycity.com, Inc., its officers, employees, agents, subsidiaries, affiliates and other partners from any direct, indirect, incidental, special, consequential or exemplary damages resulting from i) your use of Mycity.com; ii) unauthorized access to or alteration of your communications with or through Mycity.com, or iii) any other matter relating to Mycity.com. Any business transactions which may arise between users from their use of Mycity.com are the sole responsibility of the users involved.
</p>
<p>
Without limitation of the terms and conditions set forth in our Privacy Policy, you understand and agree that Mycity.com may disclose personally identifiable information if required to do so by law or in the good faith belief that such disclosure is reasonably necessary to comply with legal process, enforce this User Agreement, or protect the rights, property, or safety of Mycity.com, its users, and the public.
</p>


<h2>Disclaimer of Warranties</h2>
<p>
YOU UNDERSTAND AND AGREE THAT THE Mycity.com SERVICE IS PROVIDED ON AN “AS IS” AND “AS AVAILABLE” BASIS AND THAT Mycity.com DOES NOT ASSUME ANY RESPONSIBILITY FOR PROMPT OR PROPER DELIVERY, OR RETENTION OF ANY USER INFORMATION OR COMMUNICATIONS BETWEEN USERS. Mycity.com ASSUMES NO RESPONSIBILITY FOR THE ACCURACY OR EXISTENCE OF ANY COMMUNICATIONS BETWEEN USERS. Mycity.com EXPRESSLY DISCLAIMS ALL WARRANTIES OF ANY KIND, WHETHER EXPRESS OR IMPLIED, INCLUDING, BUT NOT LIMITED TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT.
Mycity.com MAKES NO WARRANTY THAT (i) THE Mycity.com SERVICE WILL MEET YOUR REQUIREMENTS, (ii) THE Mycity.com SERVICE WILL BE UNINTERRUPTED, TIMELY, SECURE, OR ERROR-FREE, (iii) THE RESULTS THAT MAY BE OBTAINED FROM USE OF THE Mycity.com SERVICE WILL BE ACCURATE OR RELIABLE, (iv) THE QUALITY OF ANY PRODUCTS, SERVICES, INFORMATION, OR OTHER MATERIAL PURCHASED OR OBTAINED BY YOU THROUGH THE Mycity.com SERVICE WILL MEET YOUR EXPECTATIONS, AND (V) ANY ERRORS IN THE SOFTWARE WILL BE CORRECTED.

</p>
<p>
ANY MATERIAL DOWNLOADED OR OTHERWISE OBTAINED THROUGH THE USE OF THE Mycity.com SERVICE IS DONE AT YOUR OWN DISCRETION AND RISK AND THAT YOU WILL BE SOLELY RESPONSIBLE FOR ANY DAMAGE TO YOUR COMPUTER SYSTEM OR LOSS OF DATA THAT RESULTS FROM THE DOWNLOAD OF ANY SUCH MATERIAL.
</p>
<p>
NO ADVICE OR INFORMATION, WHETHER ORAL OR WRITTEN, OBTAINED BY YOU FROM Mycity.com SHALL CREATE ANY WARRANTY NOT EXPRESSLY STATED IN THIS USER AGREEMENT.
</p>
<p>
SOME JURISDICTIONS DO NOT ALLOW THE EXCLUSION OF CERTAIN WARRANTIES OR THE LIMITATION OR EXCLUSION OF LIABILITY FOR INCIDENTAL OR CONSEQUENTIAL DAMAGES. ACCORDINGLY, SOME OF THE ABOVE LIMITATIONS MAY NOT APPLY TO YOU.
</p>

 
<h2>Entire Agreement</h2>
<p>The User Agreement constitutes the entire agreement between you and Mycity.com and governs your use of the Mycity.com Service, superseding any prior agreements between you and Mycity.com.</p>


<h2>Limitation of Liability</h2>
<p>
YOU EXPRESSLY UNDERSTAND AND AGREE THAT Mycity.com SHALL NOT BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL OR EXEMPLARY DAMAGES, INCLUDING BUT NOT LIMITED TO, DAMAGES FOR LOSS OF PROFITS, GOODWILL, USE, DATA OR OTHER INTANGIBLE LOSSES (EVEN IF Mycity.com HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES). SOME JURISDICTIONS DO NOT ALLOW THE EXCLUSION OF THE LIMITATION OR EXCLUSION OF LIABILITY FOR INCIDENTAL OR CONSEQUENTIAL DAMAGES. ACCORDINGLY, SOME OF THE ABOVE LIMITATIONS MAY NOT APPLY TO YOU. IN NO EVENT WILL Mycity.com's TOTAL CUMULATIVE DAMAGES EXCEED US$ 100.
</p> 

<h2>Governing Law</h2>
<p>
The User Agreement between you and Mycity.com, Inc. will be governed by and construed in accordance with the laws of the State of Delaware without regard to conflict of laws principles.
</p> 


<h2>General Information</h2>
<p>
Mycity.com, the Mycity.com logo, and other Mycity.com logos and names are trademarks of Mycity.com, Inc. You agree not to display or use these trademarks in any manner without Mycity.com's prior, written permission. The section titles of this User Agreement are displayed for convenience only and have no legal effect.
</p>
<p>
Please send any questions or comments, or report violations of the User Agreement to:
</p>

<p>
<address>
<strong>Mycity.com, Inc.</br>
Attn: User Agreement Issues<br/>
1991 Main Street Suite 209<br/>
Sarasota, FL 34236
</strong></address>
</p>				
				</div> 
				</div> 
            </div> 
    </section> 

<?php include("footer.php") ?>